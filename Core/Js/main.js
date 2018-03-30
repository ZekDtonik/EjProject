
    $( "#limpar" ).click(function() {
        $('input').val("");
      });

      
$(".modal-wide").on("show.bs.modal", function() {
    var height = $(window).height() - 200;
    $(this).find(".modal-body").css("max-height", height);
  });


var getAllViewsBtns = $(document.body).find(".makeViewed");
 if(getAllViewsBtns.length){
     for(i=0;i< getAllViewsBtns.length; i++){
         $(getAllViewsBtns[i]).on("click",function () {
             var url = $(this).attr("view-url");
             $.ajax({
                 url: url,
                 type: 'GET',
                 success:function(ev){
                     console.log("View Saved");
                 }
             });
         })
     }

 }

