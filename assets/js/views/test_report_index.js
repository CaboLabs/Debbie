// when each test is clicked
$(".collapse-item").on("click", function ()
{
  var id_test_case = $(this).attr("id");
  var card_test = 'card_' + id_test_case;

  var id_li_suite_this = $(this).parent().attr("id");
  var this_suite = id_li_suite_this.substring(9);

  if (id_test_case === card_test.substring(5))
  {
    $(this).addClass("active");
    $('#title_suite').show();
    $('#title_suite').html(this_suite);
    $('#Card_suites').show();
    $('.row_testcases').show();
    $('.suites_test').show();

    $('.row_testcases').not('#card_' + id_test_case).hide();
    $('.card_summary_suites').hide();
    $('#headCardSummary').hide();
    $('#cardSummaryTables').hide();
  }

  $('.collapse-item').not(this).removeClass("active");

  $("#" + this_suite).on("click", function ()
  {
    $('#Card_suites').find('.row_testcases').show();
  });
});


//when you click on each suite on the menu
$('.nav-link').on("click", function ()
{
  var id_li = $(this).attr("id");
  var class_card = 'card_' + id_li;
  $('#Card_suites').find('.row_testcases').show();

  AllSuite(id_li , class_card);

  $('.nav-item').not(this).removeClass("active");
  $('.collapse-item').removeClass("active");
});

function AllSuite(id_li , class_card)
{
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
    }
  }
  else if (id_li === class_card.substring(5))
  {
    $(this).addClass("active");
    $('#Card_suites').show();
    $('#title_suite').show();
    $('#title_suite').html(id_li);
    $('#card_summary_' + id_li).show();
    $('.suites_test').show();
    $('#Card_suites').find('.' + class_card).show();


    $('.suites_test').not('.' + class_card).hide();
    $('.card_summary_suites').not('#card_summary_' + id_li).hide();
    $('#headCardSummary').hide();
    $('#cardSummaryTables').hide();
  }
}