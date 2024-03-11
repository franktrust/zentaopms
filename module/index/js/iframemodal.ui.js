$.fn.triggerHandler = $.fn.trigger;
['registerRender', 'fetchContent', 'loadTable', 'loadPage', 'postAndLoadPage', 'loadCurrentPage', 'parseSelector', 'toggleLoading', 'openUrl', 'openPage', 'goBack', 'registerTimer', 'loadModal', 'loadTarget', 'loadComponent', 'loadPartial', 'reloadPage', 'selectLang', 'selectTheme', 'selectVision', changeAppLang, 'changeAppTheme', 'setImageSize', 'showMoreImage', 'autoLoad', 'loadForm'].forEach(function(name){window[name] = parent.parent[name];});

$(function()
{
    $('.zin-page-css').appendTo('head');

    function resizeModal()
    {
        const $modal = $('body>.modal-dialog>.modal-content>.modal-body,.modal-body').first();
        const height = $modal.outerHeight();
        $modal.closest('body').height(height);
    }

    $.ajax(
    {
        url:     modalOpenUrl,
        headers: {'X-ZUI-Modal': 'true'},
        success: function(data)
        {
            const $body = $('body');
            $body.html(data);
            const resizeOb = new ResizeObserver(resizeModal);
            resizeOb.observe($('body>.modal-dialog>.modal-content>.modal-body,.modal-body')[0]);
            resizeModal();
            $body.zuiInit().removeClass('invisible');

            if(parent.$('iframe[name="' + window.name + '"]').closest('.modal-dialog').find('.modal-header>.close').length) $('body>.modal-dialog>.modal-content>.modal-actions>[data-dismiss="modal"]').hide();
        }
    });

    $(document).on('click', '[data-dismiss="modal"]', function()
    {
        parent.$.closeModal();
    });
});