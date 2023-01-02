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
  }

  $('.nav-item').not(this).removeClass("active");
});

$(".collapse-item").on("click", function (e)
{
  var id_test_case = $(this).attr("id");
  var card_test = 'card_' + id_test_case;

  var id_li_suite_this = $(this).parent().attr("id");
  var class_card = 'card_' + id_li_suite_this.substring(9);


  if (id_test_case === card_test.substring(5))
  {
    $(this).addClass("active");

  }

  $('.collapse-item').not(this).removeClass("active");
  console.log(class_card);
});