$(document).ready(function () {
  $(window).on("scroll", function () {
    var scrollTop = $(window).scrollTop();
    if (scrollTop > 15) {
      $(".section-1").css(
        "background",
        "linear-gradient(to right top, #6d327c, #485DA6, #00a1ba, #00BF98, #36C486)"
      );

      $(".section-1 p").css("color", "white");
      $(".section-1 h1").css("color", "white");
    } else {
      $(".section-1").css("background", "linear-gradient(#FFCB9C,#EB65B8)");
      $(".section-1 p").css("color", "#1e3264");
      $(".section-1 h1").css("color", "#1e3264");
    }

    var scrollPercentage =
      ($(window).scrollTop() / ($(document).height() - $(window).height())) *
      100;
    var remainingPercentage = 100 - scrollPercentage;

    if (remainingPercentage <= 20) {
      // Do something when 30% of the page is remaining to scroll
      $(".section-3").css(
        "background",
        "linear-gradient(to right top, #6d327c, #485DA6, #00a1ba, #00BF98, #36C486)"
      );

      $(".section-3 p").css("color", "white");
      $(".section-3 h1").css("color", "white");
      $(".top-btn").css("color", "white");
    } else {
      $(".section-3").css("background", "linear-gradient(#FFCB9C,#EB65B8)");
      $(".section-3 p").css("color", "#1e3264");
      $(".section-3 h1").css("color", "#1e3264");
      $(".top-btn").css("color", "black");
    }
  });
});
