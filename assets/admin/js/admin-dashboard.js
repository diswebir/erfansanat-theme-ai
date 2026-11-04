(function(){'use strict';
document.querySelectorAll('[data-es-save-options]').forEach(function(btn){btn.addEventListener('click',function(){
 var form=document.querySelector('[data-es-options-form]'); if(!form) return;
 var data=new FormData(form); data.append('action','erfansanat_save_options');
 fetch(window.esAdminData.ajaxUrl,{method:'POST',body:data,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){ alert(res.data&&res.data.message?res.data.message:'انجام شد'); });
});});
})();
