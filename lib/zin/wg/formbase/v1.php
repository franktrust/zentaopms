<?php
declare(strict_types=1);
/**
 * The formBase widget class file of zin module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      sunhao<sunhao@easycorp.ltd>
 * @package     zin
 * @link        http://www.zentao.net
 */
namespace zin;

/**
 * 基础表单（formBase）部件类，支持 Ajax 提交
 * The formBase widget class
 */
class formBase extends wg
{
    protected static array $defineProps = array(
        'id?: string="$GID"',           // ID，如果不指定则自动生成（使用 zin 部件 GID）。
        'tagName?: string="form"',      // 标签名。
        'method?: "get"|"post"="post"', // 表单提交方式。
        'url?: string',                 // 表单提交地址。
        'enctype?: string',             // 表单提交类型。
        'actions?: array',              // 表单操作按钮，如果不指定则使用默认行为的 “保存” 和 “返回” 按钮。
        'actionsClass?: string',        // 表单操作按钮栏类名。
        'target?: string="ajax"',       // 表单提交目标，如果是 `'ajax'` 提交则为 ajax，在禅道中除非特殊目的，都使用 ajax 进行提交。
        'submitBtnText?: string',       // 表单提交按钮文本，如果不指定则使用 `$lang->save` 的值。
        'cancelBtnText?: string',       // 表单取消按钮文本，如果不指定则使用 `$lang->goback` 的值。
        'back?: string="APP"',          // 表单返回行为。
        'backUrl?: string',             // 表单返回链接。
        'ajax?:array'                   // Ajax 表单选项。
    );

    protected static array $defineBlocks = array(
        'actions' => array('toolbar')
    );

    protected function created()
    {
        if($this->prop('actions') !== null) return;

        $actions = isAjaxRequest('modal') ? array('submit') : array('submit', 'cancel');
        $this->setDefaultProps(array('actions' => $actions));
    }

    protected function buildActions(): node|array|null
    {
        if($this->hasBlock('actions')) return $this->block('actions');

        $actions = $this->prop('actions');
        if(empty($actions)) return null;

        global $lang;
        $submitBtnText = $this->prop('submitBtnText');
        $cancelBtnText = $this->prop('cancelBtnText');
        $backUrl       = $this->prop('backUrl');
        $back          = $this->prop('back');
        if(empty($submitBtnText)) $submitBtnText = $lang->save;
        if(empty($cancelBtnText)) $cancelBtnText = $lang->goback;
        foreach($actions as $key => $action)
        {
            if($action === 'submit')     $actions[$key] = array('text' => $submitBtnText, 'btnType' => 'submit', 'type' => 'primary');
            elseif($action === 'cancel') $actions[$key] = array('text' => $cancelBtnText, 'url' => $backUrl, 'back' => $back);
            elseif(is_string($action))   $actions[$key] = array('text' => $action);
        }

        return toolbar
        (
            set::className('form-actions', $this->prop('actionsClass')),
            set::items($actions)
        );
    }

    protected function buildContent(): array|node|null
    {
        return null;
    }

    public function children(): array
    {
        $children = parent::children();
        $children[] = $this->buildContent();
        $children[] = $this->buildActions();
        return $children;
    }

    protected function buildProps(): array
    {
        list($url, $target, $method, $id, $enctype, $tagName) = $this->prop(array('url', 'target', 'method', 'id', 'enctype', 'tagName'));
        $props = array
        (
            set::id($id),
            set::className('form load-indicator no-morph', $target === 'ajax' ? 'form-ajax' : '')
        );
        if($tagName === 'form')
        {
            $props[] = set(array
            (
                'action'  => empty($url) ? $_SERVER['REQUEST_URI'] : $url,
                'target'  => $target === 'ajax' ? null: $target,
                'enctype' => $enctype,
                'method'  => $method
            ));
        }
        else
        {
            $props[] = set(array
            (
                'data-action'  => $url,
                'data-target'  => $target,
                'data-enctype' => $enctype,
                'data-method'  => $method
            ));
        }
        if($target === 'ajax')
        {
            $props = array_merge($props, zui::create('ajaxForm', $this->prop('ajax')));
        }
        return $props;
    }

    protected function build()
    {
        $tagName = $this->prop('tagName');
        return h::$tagName
        (
            $this->buildProps(),
            set($this->getRestProps()),
            $this->children()
        );
    }
}
