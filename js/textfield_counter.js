/*global jQuery, Drupal*/
/*jslint white:true, multivar, this, browser:true*/

(function ($, Drupal)
{
	"use strict";

	function addClass(element, className)
	{
		element.addClass(className);
	}

	function removeClass(element, className)
	{
		element.removeClass(className);
	}

	function checkClasses(element, remaining)
	{
		if(remaining <= 5 && remaining >= 0)
		{
			removeClass(element, "textcount_over");
			addClass(element, "textcount_warning");
		}
		else if(remaining < 0)
		{
			removeClass(element, "textcount_warning");
			addClass(element, "textcount_over");
		}
		else
		{
			removeClass(element, "textcount_warning textcount_over");
		}
	}

	function textWatcher(settings)
	{
		$.each(settings.textfieldCounter, function(index)
		{
			var fieldSettings = settings.textfieldCounter[index];

			$.each(fieldSettings.key, function(index)
			{
				$("." + fieldSettings.key[index]).once("textfield-counter-text-watcher").each(function()
				{
					var counter, maxlength, remaining;

					maxlength = fieldSettings.maxlength;
					remaining = maxlength - Number($(this).val().length);
					counter = $("<div/>", {class:"textfield_counter_counter"}).html(Drupal.t("Remaining: <span class='remaining_count'>@count</span>", {"@count":remaining}));

					if(fieldSettings.counterPosition === "before")
					{
						counter.insertBefore($(this));
					}
					else
					{
						counter.insertAfter($(this));
					}

					checkClasses($(this).parent(), remaining);

					$(this).keyup(function()
					{
						remaining = maxlength - Number($(this).val().length);
	
						counter.children(".remaining_count:first").text(remaining);
	
						checkClasses($(this).parent(), remaining);
					});
				});
			});
		});
	}

	function formSubmitListener(context)
	{
		$(context).find("form").once("textfield-counter-form-submit-listener").each(function()
		{
			$(this).submit(function(e)
			{
				var errorElement = $(this).find(".textcount_over:first");
				if(errorElement.length)
				{
					e.preventDefault();
					$("html, body").animate({
						scrollTop: $(errorElement).offset().top
					}, 300);
				}
			});
		});
	}

	Drupal.behaviors.textfieldCounterTextarea = {
		attach:function(context, settings)
		{
			textWatcher(settings);
			formSubmitListener(context);
		}
	};

}(jQuery, Drupal));
