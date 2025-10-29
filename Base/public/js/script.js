$(document).on('ready', function() {
    showAlerts();
})

function showAlerts() {
    let i = 250;
    $(".alert").each(function() {

        console.log(i);
        setTimeout(showAlertAnimation(this), i);
        i = i + 250;
    })
}

function showAlertAnimation(el) {
    $(el).animate({ opacity: '1', right: "-50px" }, 500).delay(500);
}

$('.alert button').on('click', function() {
    removeAlert($(this).closest('.alert'));
})

setTimeout(removeAlerts, 15000);

function removeAlerts() {
    i = 500;
    $(".alert").each(function() {
        setTimeout(removeAlert(this), i);
        i = i + 500;
    })
}

$('#tchatModalUsers form, form.tchatRoom-updater').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        method: "POST"
     })
    .done(function(data) {
        window.location.reload();
      })
    ;
})

$('#tchatSideNav .tchat').on('click', function () {
    //$.ajax({
    //    url: $(this).closest('button.tchat').attr('url'),
    //    method: "GET"
    // })
    //.done(function(data) {
    //    let tchat = getTchat(data.room, data.connectedId);
    //    $(tchat).attr('messenger-url', data.messageUrl);
    //    $(tchat).attr('update-url', data.messagesUpdateUrl);
//
    //    $('#tchatContainer').append(tchat);
    //  })
    //;


})

function getTchat(data, userId)
{
    var newDiv = document.createElement("div");
    $(newDiv).addClass('rounded');
    $(newDiv).addClass('room-messenger');
    $(newDiv).addClass('border');
    $(newDiv).addClass('bg-white');
    $(newDiv).attr('room-id', data.id);
    $(newDiv).attr('user-id', userId);

    var messagesContainer = document.createElement("div");
    $(messagesContainer).attr('id', 'messages_' + data.id);
    $(messagesContainer).addClass('collapse');
    $(messagesContainer).addClass('p-2');

    var messagescontent = document.createElement("div");
    $(messagescontent).addClass('messages');

    data.messages.forEach(function (item) {
        var message = showMessage(item, userId);
        $(messagescontent).append(message);
    });

    $(messagesContainer).append(messagescontent);
    $(newDiv).append(messagesContainer);

    var input = document.createElement("input");
    $(input).attr('type', 'text');
    $(input).addClass('form-control');

    var form = document.createElement("form");
    $(form).append(input);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let container = $(this).closest('.room-messenger');

        let messagesContainer = container.find('.messages');

        let txt = $(this).find('input').val();

        $.ajax({
            url: $(this).closest('.room-messenger').attr('messenger-url'),
            method: "POST",
            data: {text: txt}
         })
        .done(function(data) {
            var messagescontent = showMessage(data, parseInt(container.attr('user-id')));
            $(messagesContainer).append(messagescontent);
          })
        ;
    })

    $(messagesContainer).append(form);

    var button = document.createElement("button");
    $(button).addClass('btn');
    $(button).addClass('btn-light');
    $(button).addClass('btn-block');
    $(button).addClass('d-flex');
    $(button).addClass('justify-content-between');
    $(button).addClass('rounded-0');
    $(button).addClass('align-items-center');
    $(button).html(data.name + '<i class="fas fa-expand-alt"></i>');
    $(button).attr('data-toggle', 'collapse');
    $(button).attr('data-target', 'messages_' + data.id);
    $(button).attr('aria-expanded', 'false');

    button.addEventListener('click', function () {
        $(messagesContainer).toggle();
    });

    var actionContainer = document.createElement("div");
    $(actionContainer).addClass('d-flex');
    $(actionContainer).addClass('justify-content-between')
    $(actionContainer).append(button);

    var closeButton = document.createElement("button");
    $(closeButton).addClass('close');
    $(closeButton).addClass('px-2');
    $(closeButton).html('<i class="fas fa-times"></i>');
    $(actionContainer).append(closeButton);

    closeButton.addEventListener('click', function () {
        $(this).closest('.room-messenger').remove();
    });

    $(newDiv).append(actionContainer);

    return newDiv;
}



//setInterval(function() {
//    var messageCount = 0;
//    $('.room-messenger').each(function () {

//        let container = $(this);
//        let messagesContainer = container.find('.messages');

        
//        console.log(container.attr('update-url'));
        //$.ajax({
        //    url: container.attr('update-url'),
        //    method: "GET"
        //    })
        //.done(function(data) {
        //    console.log(data);
        //    messageCount += data.room.messages.length;
//
        //    data.room.messages.forEach(function (item) {
        //        let created = messagesContainer.find('[message-id="' + item.id + '"]');
        //        
        //        if (created.length === 0) {
        //            var message = showMessage(item, data.connectedId);
        //            $(messagesContainer).append(message);
        //        }
        //    });
        //});

        //let last = messagesContainer.find('div').last();
        
        //if (last.length > 0) {
        //    if (messagesContainer[0].getBoundingClientRect().top + $(messagesContainer).height() < last[0].getBoundingClientRect().top + $(last).height()) {
        //        messagesContainer[0].scroll({
        //            top: last[0].getBoundingClientRect().top + $(last).height(),
        //            behavior: "smooth",
        //        });
        //    }
        //}
 //   })

//    var badge = $("#tchatMenu").find('span');

//    if (messageCount > 0) {
//        if (badge) {
//            badge.text(messageCount);
//        } else {
//            $("#tchatMenu").append('<span class="room-messages">' + messageCount + '</span>')
//        }
//    }
    
//}, 2000)

$('.hide-tchat').on('click', function() {
    console.log($(this).closest('.room-messenger'));
    $(this).closest('.room-messenger').collapse('hide');
})

function showMessage(item, userId)
{
    var message = document.createElement("div");
    $(message).addClass('mb-2');
    $(message).addClass('rounded');
    $(message).attr('message-id', item.id);

    console.log(item);
    if (item.author.id === userId) {
        $(message).addClass('my-message');
    } else {
        $(message).addClass('distant-message');
    }

    var author = document.createElement("div");
    $(author).addClass('d-flex');
    $(author).addClass('justify-content-between');

    var userRef = document.createElement("p");
    $(userRef).addClass('mb-0');
    $(userRef).html(item.author.username);

    var hour = document.createElement("p");
    $(hour).html('<small>' + item.createdAt.hour + '</small>');
    $(hour).addClass('mb-0');

    $(author).append(userRef);
    $(author).append(hour);

    $(message).append(author);
    var text = document.createElement("p");
    $(text).text(item.content);
    $(message).append(text);

    return message;
}

function removeAlert(element) {
    $(element).animate({ opacity: '0', right: "0" }, 500).hide(0, function() {
        $(this).remove();
    });
}

$('input[name="duration"]').on('change', function() {
    var value = $(this).val();

    var value = value.replaceAll(',', '.');

    $(this).val(roundByNum(parseFloat(value)));
})

function roundByNum(num, rounder) {
    var multiplier = 1 / (rounder || 0.5);
    return Math.round(num * multiplier) / multiplier;
}

$('input[type="file"]').on('change', function() {
    const file = this.files;
    const text = $('#headshot_filename');

    if (file) {
        const preview = $('label[for="' + this.id + '"] > img');

        if (preview.length > 0) {
            preview.fadeOut(0);

            const fileReader = new FileReader();
            fileReader.onload = function(event) {
                preview.attr('src', event.target.result);
            }
            fileReader.readAsDataURL(file[0]);

            preview.fadeIn(500);
        } else {
            const label = $('label[for="' + this.id + '"].profile');

            console.log(label);
            const fileReader = new FileReader();
            fileReader.onload = function(event) {
                var img = document.createElement("img");

                img.setAttribute('src', event.target.result);

                label.html(img);
            }

            fileReader.readAsDataURL(file[0]);

            label.fadeIn(500);
        }

        if (text.length > 0) {
            console.log(file[0].name)
            text.text(file[0].name);
            text.show('slow');
        }

    }
})

$('.datepicker').datepicker({
    dateFormat: "dd/mm/yy"
});

function scrollToId() {
    const container = document.getElementById(`calendar`);
    const section = document.getElementById(`today`);

    if (container !== null && section !== null) {
        let destination = (container.getBoundingClientRect().width - section.getBoundingClientRect().left) * -1;
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
   

    var inputDeadline = $(this).closest('.calendar_head').find('form input[name="deadline"]');
    
    if (inputDeadline) {
        var firstSelect = $(this).closest('.calendar_head').find('.moment span.btn-sm.btn-primary');

        if (firstSelect.length > 0) {

            console.log(firstSelect[0]);
            if ($(firstSelect[0]).attr('date-value') < $(this).attr('date-value')) {
                let startDate = $(firstSelect[0]).attr('date-value');
                let endDate = $(this).attr('date-value');
                let endHalf = $(this).attr('half-day');
                let startHalf = $(firstSelect[0]).attr('half-day');

                
                $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');

                $(this).closest('.calendar_head').find('.moment span.btn-sm').each(function() {
                    if ($(this).attr('date-value') >= startDate && $(this).attr('date-value') <= endDate) {
                        if ($(this).attr('date-value') > startDate && $(this).attr('date-value') < endDate) {
                            $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
                        }

                        if ($(this).attr('date-value') == endDate && $(this).attr('half-day') == 'AM' && endHalf == 'PM') {
                            $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
                        }

                        if ($(this).attr('date-value') == startDate && $(this).attr('half-day') == 'PM' && startHalf == 'AM') {
                            $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
                        }
                        
                    }
                });

                $(this).closest('.calendar_head').find('form input[name="startDate"]').val($(this).attr('date-value'));
                $(this).closest('.calendar_head').find('form input[name="halfDay"]').val($(this).attr('half-day'));
                $(this).closest('.calendar_head').find('form input[name="deadline"]').val(endDate);
            }
        } else {
            $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
        }
        
    } else {
        $('.moment span.btn-primary').removeClass('btn-primary').removeClass('disabled').addClass('text-primary').addClass('btn-light');
        $(this).closest('.calendar_head').find('form input[name="startDate"]').val($(this).attr('date-value'));
        $(this).closest('.calendar_head').find('form input[name="halfDay"]').val($(this).attr('half-day'));
        $(this).removeClass('btn-light').addClass('btn-primary').addClass('disabled').removeClass('text-primary');
        $(this).closest('.calendar_head').find('form .duration-number').show('slow');
        $(this).closest('.calendar_head').find('form select[name="client"]').show();
    }
    
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

$('.copy-btn').on('click', function() {
    let target = $($(this).attr('data_target')).text();

    navigator.clipboard.writeText(target);

    $('#alert_container').append('<div class="alert alert-success">' + $(this).attr('copied-message') + '<button type="button" onclick="removeAlert($(this).closest(\'.alert\'))" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

    showAlerts();

    setTimeout(removeAlerts, 15000);
})

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

    $('#loader').fadeOut(0, function() {
        $('body>:not(#loader):not(.sf-toolbar)').fadeIn(1000);
    });
}

function showSpinner() {
    $('body>:not(#loader)').fadeOut("slow", function() {
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

$('#tchatMenu').on('click', function () {
    $('#tchatSideNav').toggle('slow');
})

$('#tchatSideNav .btn-close').on('click', function () {
    $('#tchatSideNav').hide('slow');
})

$(function() {
    var dateFormat = "dd/mm/yy",
      from = $( "input#absence_from" )
        .datepicker({
          defaultDate: "+1d",
          changeMonth: true,
          dateFormat: dateFormat,
          numberOfMonths: 1
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "input#absence_to" ).datepicker({
        defaultDate: "+2d",
        changeMonth: true,
        dateFormat: dateFormat,
        numberOfMonths: 1
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
  }
);

$( function() {
    console.log($( ".datetime-picker.start-range" ));
    var dateFormat = "mm/dd/yy",
        from = $( ".datetime-picker.start-range" )
        .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            defaultDate: new Date(),
            format:'DD/MM/YYYY HH:mm'
        })
        .on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
        }),
        to = $( ".datetime-picker.to-range" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1
        })
        .on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
        });

    function getDate( element ) {
        var date;
        try {
        date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
        date = null;
        }

        return date;
    }
} );

$('[event-list]').hover(function() 
    {
        $(this).addClass('box-shadow');
        $('[calendar-event="' + $(this).attr('event-list') + '"]').removeClass('bg-primary-light').addClass('bg-primary');
    },
    function()
    {
        $(this).removeClass('box-shadow');
        $('[calendar-event="' + $(this).attr('event-list') + '"]').removeClass('bg-primary').addClass('bg-primary-light');
    }
)

$('[event-list]').hover(function() 
    {
        $(this).addClass('box-shadow');
        $('[calendar-event="' + $(this).attr('event-list') + '"]').removeClass('bg-primary').addClass('bg-primary-light');
    },
    function()
    {
        $(this).removeClass('box-shadow');
        $('[calendar-event="' + $(this).attr('event-list') + '"]').removeClass('bg-primary-light').addClass('bg-primary');
    }
)

$('[calendar-event]').hover(
    function() {
        $('[calendar-event="' + $(this).attr('calendar-event') + '"]').removeClass('bg-primary').addClass('bg-primary-light');
        $('[event-list="' + $(this).attr('calendar-event') + '"]').addClass('box-shadow');
    },
    function() {
        $('[calendar-event="' + $(this).attr('calendar-event') + '"]').removeClass('bg-primary-light').addClass('bg-primary');
        $('[event-list="' + $(this).attr('calendar-event') + '"]').removeClass('box-shadow');
    }
)