
$(document).ready(function() {
$("li.nav-item").on("click",function(){

  var id_li = $("a", this).attr("id");
  var class_card = 'card_' + id_li;

  if (id_li == 'dashboard') 
  {
    $('#cardSummaryTables').show();
    $('#headCardSummary').show();
    $(this).addClass("active");
    $('h2').hide();
    $('#Card_suites').hide();
    $('#' + id_li).attr('aria-expanded', 'false');
    $('#' + id_li).addClass("collapsed");
    $('#collapseUtilities_' + id_li).removeClass("show");
  }
  else
  if (id_li === class_card.substring(5)) 
  {
    $('#Card_suites').before('<h2 id="h2_' + class_card.substring(5) + '">' + class_card.substring(5) + '</h2>');
    $('#Card_suites').find('.' + class_card).show();
    $(this).addClass("active");
  
    $('.suites_test').not('.' + class_card).hide();
    $('h2').not('#h2_' + class_card.substring(5)).hide();
    $('#headCardSummary').hide();
    $('#cardSummaryTables').hide();
  }

  $('.nav-item').not(this).removeClass("active"); 
});
});