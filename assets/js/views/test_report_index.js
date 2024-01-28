$("li.nav-item").on("click", function (e)
{
  var id_li = $("a", this).attr("id");
  var class_card = 'card_' + id_li;

  if (id_li === 'dashboard')
  {
    $('#cardSummaryTables').show();
    $('#headCardSummary').show();
    $(this).addClass("active");
    $('#title_suite').hide();
    $('#Card_suites').hide();

    if ($('li > div').hasClass('show'))
    {
      $('li > div').collapse('hide');
      console.log($('li > div'));
    }
  }
  else if (id_li === class_card.substring(5))
  {
    $('#Card_suites').show();
    $('#title_suite').show();
    $('#title_suite').html(id_li);
    $('#Card_suites').find('.' + class_card).show();
    $(this).addClass("active");

    $('.suites_test').not('.' + class_card).hide();
    $('#headCardSummary').hide();
    $('#cardSummaryTables').hide();

    $('#success_cases [id]').each(function(){
      let suites_test = $(this).html();
      $('#card_' + suites_test).show();
    });
  }

  $('.nav-item').not(this).removeClass("active");
});

$("#table_failed_cases").on("click", function (e)
{
  $('#title_suite').text('Failed Cases');
  $('#title_suite').show();
  $('#Card_suites').show();
  $('#headCardSummary').hide();
  $('#cardSummaryTables').hide();
  $('.suites_test').show();
 
  $('#success_cases [id]').each(function(){
    let suites_test = $(this).html();
    $('#card_' + suites_test).hide();
  });
});

$("#table_failed_cases").on("mouseover", function (e)
{
  $(this).css('cursor', 'pointer');
});