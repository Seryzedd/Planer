$(document).on('ready', function() {
    $(".alert").animate({ opacity: '1', right: "-=50" }, 500);
})

$('.alert').on('click', function() {
    removeAlert($(this).closest('.alert'));
})

setTimeout(removeAlerts, 15000);

function removeAlerts() {
    $(".alert").each(function() {
        removeAlert(this);
    })
}

function removeAlert(element) {
    $(".alert").animate({ opacity: '0', right: "0" }, 500).hide('slow');
}

$('.datepicker').datepicker({
    dateFormat: "dd/mm/yy"
});

function scrollToId() {
    const container = document.getElementById(`calendar`);
    const section = document.getElementById(`today`);

    if (container !== null && section !== null) {
        let destination = (container.getBoundingClientRect().width - section.getBoundingClientRect().left) * -1;
        console.log(section.getBoundingClientRect().left);
        container.scrollLeft += section.getBoundingClientRect().left;
    }
}

$('#calendar').on('scroll', function() {
    const container = document.getElementById("calendar");
    const section = document.getElementById(`today`);

})

$('.calendar-project button[assign-target]').on('click', function() {
    $(this).closest('.calendar-project').find('.form-group').hide();

    $('.form-group' + $(this).attr('assign-target') + '').show();
})

$('.duration-input-number i').on('click', function() {

    if ($(this).hasClass('fa-minus')) {
        let input = $(this).closest('.duration-input-number').find('input[type="text"]');
        input.val(parseFloat(input.val()) - 0.5);

        console.log(input.val());
    }

    if ($(this).hasClass('fa-plus')) {
        let input = $(this).closest('.duration-input-number').find('input[type="text"]');
        input.val(parseFloat(input.val()) + 0.5);
    }
})

$('.moment span.btn-sm').on('click', function() {
    console.log(this);
    $('.moment span.btn-primary').removeClass('btn-primary').removeClass('disabled').addClass('text-primary').addClass('btn-light');
    $(this).closest('.calendar_head').find('form input[name="startDate"]').val($(this).attr('date-value'));
    $(this).closest('.calendar_head').find('form input[name="halfDay"]').val($(this).attr('half-day'));
    $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
    $(this).closest('.calendar_head').find('form .duration-number').show('slow');
    $(this).closest('.calendar_head').find('form select[name="client"]').show();
})

$('select[name="project"]').on('change', function() {
    if ($(this).val() !== '') {
        $(this).closest('form').find('select[name="client"]').hide();
    } else {
        $(this).closest('form').find('select[name="client"]').show();
    }
})

$('#toggleUsers').on('click', function() {
    $('.calendar-user').toggle();
    if ($('.calendar-user')[0].style.display === "none") {
        $(this).find('.fa-eye-slash').hide();
        $(this).find('.fa-eye').show('slow');
    } else {
        $(this).find('.fa-eye').hide();
        $(this).find('.fa-eye-slash').show('slow');
    }
})

$('.collapse').on('show.bs.collapse', function() {
    let button = $('[data-toggle][data-target="#' + $(this).attr('id') + '"]');

    if (button.attr('expanded-txt')) {
        button.text(button.attr('expanded-txt'));
    }
})

$('.collapse').on('hide.bs.collapse', function() {
    let button = $('[data-toggle][data-target="#' + $(this).attr('id') + '"]');

    if (button.attr('collapsed-txt')) {
        button.text(button.attr('collapsed-txt'));
    }
})

$(document).on('ready', function() {
    if ($('.calendar-user').length > 0) {
        if ($('.calendar-user')[0].style.display === "none") {
            $(this).find('.fa-eye-slash').hide();
            $(this).find('.fa-eye').show('slow');
        } else {
            $(this).find('.fa-eye').hide();
            $(this).find('.fa-eye-slash').show('slow');
        }
    }

    scrollFunction();
    hideSpinner();
})

$(window).on('beforeunload', function() {
    showSpinner();
});

//No easing
function constant(duration, range, current) {
    return duration / range;
}

//Linear easing
function linear(duration, range, current) {
    return ((duration * 2) / Math.pow(range, 2)) * current;
}

//Quadratic easing
function quadratic(duration, range, current) {
    return ((duration * 3) / Math.pow(range, 3)) * Math.pow(current, 2);
}

function animateValue(element, start, duration, easing) {

    var end = parseInt(element.text(), 10);

    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : 0;

    var obj = element;
    var startTime = new Date();
    var offset = 1;
    var remainderTime = 0;
    var step = function() {
        current += increment;
        element.text(current);

        if (current !== end) {
            setTimeout(step, easing(duration, range, current));
        } else {

        }
    };

    setTimeout(step, easing(duration, range, start));
}

if ($('.animate-number').length > 0) {
    $('.animate-number').each(function() {
        animateValue($(this), 0, 1000, linear);
    })
}

function hideSpinner() {

    $('#loader').fadeOut(1000, function() {
        $('body > :not(#loader):not(.sf-toolbar)').fadeIn("slow");
    });
}

function showSpinner() {
    $('body :not(#loader)').fadeOut("slow", function() {
        $('#loader').fadeIn(0);
    });

}

let mybutton = $("footer button");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() { scrollFunction() };

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.show('slow');
    } else {
        mybutton.hide('slow');
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}