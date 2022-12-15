$(".nav-link").on("click",function(e){
  e.preventDefault();

  var id_li = $(this).attr("id");
  var class_card = 'card_' + id_li;

  if (id_li == 'summaryTable' || id_li == 'dashboard') 
  {
    $('#cardSummaryTables').css("display", "block");
  }
  else
  {
    $('#cardSummaryTables').css("display", "none");
  }
  
  if (id_li == class_card.substring(5)) 
  {
    $('.' + class_card).css("display", "block"); 
  } 
  else 
  {
    $('.' + class_card).css("display", "none");
  }
});

