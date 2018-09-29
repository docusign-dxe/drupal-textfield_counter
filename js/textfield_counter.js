/**
 * @file
 * Adds textfield counter JavaScript.
 */

/*global jQuery, Drupal*/
/*jslint white:true, this, browser:true*/

(function ($, Drupal) {

  "use strict";

  function addClass(element, className) {
    element.addClass(className);
  }

  function removeClass(element, className) {
    element.removeClass(className);
  }

  function checkClasses(element, remaining) {
    if (remaining <= 5 && remaining >= 0) {
      removeClass(element, "textcount_over");
      addClass(element, "textcount_warning");
    }
    else if (remaining < 0) {
      removeClass(element, "textcount_warning");
      addClass(element, "textcount_over");
    }
    else {
      removeClass(element, "textcount_warning textcount_over");
    }
  }

  function textWatcher(settings) {
    $.each(settings.textfieldCounter, function (index) {
      var fieldSettings = settings.textfieldCounter[index];

      $.each(fieldSettings.key, function (index) {
        $("." + fieldSettings.key[index]).once("textfield-counter-text-watcher").each(function () {
          var counter, maxlength, currentLength, remaining, countHTML;

          maxlength = fieldSettings.maxlength;
          if (maxlength) {
            countHTML = fieldSettings.countHTMLCharacters;
            if (countHTML) {
              currentLength = $(this).val().length;
            }
            else {
              currentLength = $("<div/>").html($(this).val()).text().length;
            }
            remaining = maxlength - currentLength;
            counter = $("<div/>", {class:"textfield_counter_counter"}).html(Drupal.t("Remaining: <span class='remaining_count'>@count</span>", {"@count":remaining}));

            if (fieldSettings.counterPosition === "before") {
              counter.insertBefore($(this));
            }
            else {
              counter.insertAfter($(this));
            }

            checkClasses($(this).parent(), remaining);

            $(this).keyup(function () {
              if (countHTML) {
                currentLength = $(this).val().length;
              }
              else {
                currentLength = $("<div/>").html($(this).val()).text().length;
              }

              remaining = maxlength - currentLength;
              counter.children(".remaining_count:first").text(remaining);
              checkClasses($(this).parent(), remaining);
            });
          }
        });
      });
    });
  }

  function formSubmitListener(context, settings) {
    $(context).find("form").once("textfield-counter-form-submit-listener").each(function () {
      $(this).submit(function (e) {
        var errorElements = $(this).find(".textcount_over");
        errorElements.each(function (elementIndex) {
          $.each(settings.textfieldCounter, function (settingsIndex, fieldSettings) {
            var wrapperElement = $(errorElements[elementIndex]);
            if (fieldSettings.preventSubmit && settingsIndex === wrapperElement.find(".textfield-counter-element:first").data("field-definition-id")) {
              e.preventDefault();
              $("html, body").animate({
                scrollTop:wrapperElement.offset().top
              }, 300);
            }
          });
        });
      });
    });
  }

  Drupal.behaviors.textfieldCounterTextarea = {
    attach:function (context, settings) {
      textWatcher(settings);
      formSubmitListener(context, settings);
    }
  };

}(jQuery, Drupal));
