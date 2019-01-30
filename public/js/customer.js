jQuery(document).ready(function(){
	jQuery.ajax({
		type: "post",url: ajax.url,data: { action: 'get_masked' },
		success: function(res){
      calendar = new SabaiSaludCalendar({
        "dayStart": res.dayStart,
        "dayEnd": res.dayEnd,
        "granularity": res.granularity,
        "maxFutureDays": res.days.length,
        "startDate": res.startDate
      }, res.days, []);

			document.querySelector('#ss_scroll_target').style.display = 'block';
			document.querySelector('#selected-text').style.display = 'block';
			document.querySelector('#selected-text').innerHTML = 'Please suggest a date and time using the calendar above. <b>We will call to you confirm availability.';

      //set the selected type (and time)
      var typeInput = document.querySelector('#sabai-salud-calendar-input-type');
      calendar.selectType(typeInput.selectedOptions[0].value, typeInput.selectedOptions[0].dataset.massageLength);

			calendar.draw(document.querySelector('#ss_booking_calendar'));

      //allow the calendar to accept selections, and tell it what else to do when it does
      calendar.acceptSelection(function(dateString, time, animate){
				if(animate){
					var offset = jQuery('#ss_scroll_target').offset(); // Contains .top and .left
					offset.top -= 100;
					jQuery('html, body').animate({
					    scrollTop: offset.top,
					}, 1000);
				}
        document.querySelector('#sabai-salud-calendar-input-time').value = time;
        document.querySelector('#sabai-salud-calendar-input-date').value = dateString;
				document.querySelector('#selected-text').innerHTML = `${new Date(dateString).toString().substr(0,10)} - ${time}. <b>We will call you to confirm availability.</b>`;
      }, function(){ //callback in case selection not allowed
        delete document.querySelector('#selected-text').dataset.dateTime;
        document.querySelector('#sabai-salud-calendar-input-date').value = null;
        document.querySelector('#sabai-salud-calendar-input-time').value = null;
      });

      //if form has passed us stuff back, select the slot necessary and display that date
      if(document.querySelector('#sabai-salud-calendar-input-passed').value == "true"){
        var passedDate = new Date(document.querySelector('#sabai-salud-calendar-input-date').value);
        passedDate.setHours(0,0,0,0);
        var passedTime = document.querySelector('#sabai-salud-calendar-input-time').value;

        calendar.select(passedDate, passedTime);
        calendar.drawDay(passedDate);
      }

      //hide the date time entry components - javascript will populate these
      form.querySelector('#dateTimeEntry').style.display = 'none';

      //change the selected type when we change it
      window.addEventListener('input', function(e){
        if(e.target.id == 'sabai-salud-calendar-input-type'){
          calendar.selectType(e.target.selectedOptions[0].value, e.target.selectedOptions[0].dataset.massageLength);
        }
      });

		}
	});
});
