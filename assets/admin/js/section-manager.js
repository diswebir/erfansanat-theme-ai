(function($){'use strict';
$(function(){
 var $list=$('[data-es-sections-list]');
 if($list.length && $.fn.sortable){ $list.sortable({handle:'[data-es-section-handle]',update:function(){ $list.find('[data-es-section]').each(function(i){ $(this).find('[data-es-section-order]').val(i); }); }}); }
 $(document).on('click','[data-es-section-toggle-head]',function(){ $(this).closest('[data-es-section]').find('[data-es-section-body]').slideToggle(120); });
});
})(jQuery);
