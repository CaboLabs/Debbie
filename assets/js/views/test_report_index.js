$( document ).ready(function() {
$("li.nav-item").on("click",function(){

  var id_li = $("a", this).attr("id");
  //var li_expanded = $('#' + id_li).attr("aria-expanded","false");
  var class_card = 'card_' + id_li;

  if (id_li == 'dashboard') 
  {
    $('#cardSummaryTables').css("display", "block");
  }
  else
  {
    $('#cardSummaryTables').css("display", "none");
  }

 
  if (id_li === class_card.substring(5)) 
  {
    console.log(class_card);
    $('.' + class_card).toggle();
    $(this).addClass("active");
    $('#dashboard').removeClass("active");
  }

  if ($('li.nav-item > a').hasClass(className))
  {

  }
  

  
});


  
});