$("li.nav-item").on("click",function(){

  var id_li = $("a", this).attr("id");
  var class_card = 'card_' + id_li;

  if (id_li == 'dashboard') 
  {
    $('#cardSummaryTables').show();
  }
  else
  {
    $('#cardSummaryTables').hide();
  }
 
  if (id_li === class_card.substring(5)) 
  {
    $('#Card_suites').find('.' + class_card).show();
    $(this).addClass("active");
  
    $('.suites_test').not('.' + class_card).hide();
    console.log('#' + class_card);
  }

  $('.nav-item').not(this).removeClass("active"); 
});