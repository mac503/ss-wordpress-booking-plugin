jQuery(document).ready(function(){

	window.addEventListener('input', function(e){
		console.log(e.target);
		if(e.target.nodeName == 'INPUT') e.target.classList.add('touched');
		if(e.target.id == 'ss_new_type'){
			calendar.selectType(e.target.selectedOptions[0].value, e.target.selectedOptions[0].dataset.massageLength);
		}
	});

	SabaiSaludCalendar.prototype.highlightBooking = function(id){
		var booking = this.bookings.find(x=>x.id==id);
		if(booking) this.highlightedBooking = booking;
		this.drawDay(this.displayedDate);
	}

	SabaiSaludCalendar.prototype.clearHighlight = function(){
		this.highlightedBooking = undefined;
		this.drawDay(this.displayedDate);
	}

	SabaiSaludCalendar.prototype.allowSelectBooking = function(){
		this.mode = 'allowSelectBooking';
		this.div.querySelector('.sabai-salud-calendar-body').dataset.mode='allow-select-booking';
	}

	SabaiSaludCalendar.prototype.clearSelect = function(){
		this.selectedDate = null;
		this.selectedTime = null;
		this.drawSelected();
	}

  bookingManager = {};

	bookingManager.timeMinutes = function(time){
		return parseInt(time.split(':')[0])*60+parseInt(time.split(':')[1]);
	}

	bookingManager.addNew = function(id){
		calendar.clearHighlight();
		var orig = {
			type: bookingManager.types[0].name,
			name: '',
			surnames: '',
			email: '',
			phone: ''
		}
		if(id){
			var booking = calendar.bookings.find(x=>x.id == id);
			if(booking){
				orig = {
					type: booking.type,
					name: booking.nombre,
					surnames: booking.apellidos,
					email: booking.email,
					phone: booking.phone
				}
			}
		}
		document.querySelector('#ss_booking_current_action').style.display = 'block';
		document.querySelector('#ss_booking_current_action').innerHTML = `
			<div class='closeIcon' data-action='cancel-add-booking'></div>
			<form id='ss_new_booking_form'>
			<u>New Booking</u><p>
				<span id='ss_new_booking_date_time'><b>Select date/time from the calendar above.</b></span><br/><br/>
				<input type='hidden' id='ss_booking_new_date'></input>
				<input type='hidden' id='ss_booking_new_time'></input>
				<label for="ss_new_type">Type</label>
				<select id='ss_new_type'>
					${
						bookingManager.types.map(x=>`
							<option data-massage-length="${x.length}" value="${x.name}"${orig.type == x.name ? ' selected' : ''}>${x.name}</option>
						`).join('')
					}
				</select>
				<label for="ss_new_name">Name</label>
				<input type='text' id='ss_new_name' required value='${orig.name}'></input>
				<label for="ss_new_surnames">Surnames</label>
				<input type='text' id='ss_new_surnames' required value='${orig.surnames}'></input>
				<label for="ss_new_email">Email</label>
				<input type='email' id='ss_new_email' required value='${orig.email}'></input>
				<label for="ss_new_phone">Phone</label>
				<input type='tel' id='ss_new_phone' required value='${orig.phone}'></input>
				<input type='checkbox' id='ss_booking_email_customer'></input> <label for="ss_booking_email_customer">Email customer confirmation?</label>
			</form>
			<div id='ss_booking_item_actions'>
				<div data-action='confirm-new-booking'>Confirm New Booking</div>
				<div data-action='cancel-add-booking'>Cancel</div>
			</div>
		`;

		if(id) calendar.selectType(document.querySelector('#ss_new_type').selectedOptions[0].value, document.querySelector('#ss_new_type').selectedOptions[0].dataset.massageLength);
		else calendar.selectType(bookingManager.types[0].name, bookingManager.types[0].length);
		bookingManager.setMode('picking', function(dateString, time, animate){
			if(animate){
				var offset = jQuery('#ss_booking_current_action').offset(); // Contains .top and .left
				offset.top -= 100;
				jQuery('html, body').animate({
						scrollTop: offset.top,
				});
			}
			var booking = calendar.highlightedBooking;
			document.querySelector('#ss_new_booking_date_time').innerHTML = `
				${time} ${new Date(dateString).toString().substr(0,10)}
			`;
			document.querySelector('#ss_booking_new_date').value = dateString;
			document.querySelector('#ss_booking_new_time').value = time;
		}, function(){
			document.querySelector('#ss_new_booking_date_time').innerHTML = `
				<b>Select date/time from the calendar above.</b>
			`;
		});
	}

  bookingManager.showWorking = function(){
    document.querySelector('#ss_booking_current_action').style.display = 'block';
    document.querySelector('#ss_booking_current_action').innerHTML = `<div class='workingIndicator'></div>`;
  }

	bookingManager.setMode = function(mode, pickCallback, pickNullCallback){
		console.log(mode);
		switch(mode){
			case "selecting":
				var offset = jQuery('#ss_booking_calendar').offset(); // Contains .top and .left
				jQuery('html, body').animate({
						scrollTop: offset.top,
				});
				this.mode = 'selecting';
				bookingManager.showDayControls();
				calendar.allowSelectBooking();
				document.querySelector('#ss_booking_menu').dataset.allow = true;
			break;
			case "working":
				this.mode = 'working';
				bookingManager.showWorking();
				bookingManager.hideDayControls();
				delete document.querySelector('#ss_booking_menu').dataset.allow;
			break;
			case "picking":
				var offset = jQuery('#ss_booking_calendar').offset(); // Contains .top and .left
				jQuery('html, body').animate({
						scrollTop: offset.top,
				});
				this.mode = 'picking';
				bookingManager.hideDayControls();
				delete document.querySelector('#ss_booking_menu').dataset.allow;
				calendar.acceptSelection(pickCallback, pickNullCallback);
			break;
		}
	}

  bookingManager.showList = function(filter, mode){
		var today = new Date();
		today.setHours(0,0,0,0);
    document.querySelector('#ss_booking_list_list').innerHTML = calendar.bookings.filter(filter).slice().sort((a,b)=>{
			if(mode == 'unconfirmed' || mode == 'confirmed') return new Date(a.start) - new Date(b.start);
      else return new Date(b.start) - new Date(a.start);
    }).map(x=>`
      <div class='ss_list_item' data-name='${x.nombre} ${x.apellidos}' data-id='${x.id}'${(mode=='unconfirmed' || mode=='confirmed' || (mode=='all' && x.deleted == 0 && new Date(x.start)>today)) ? `data-action='zoom-to-booking'` : ''}>
				<div data-action='duplicate-booking' data-duplicate-id=${x.id}>Duplicate</div>
        ${x.deleted == 0 ? `${x.confirmed == 0 && x.deleted == 0 ? '<b>UNCONFIRMED</b><br/>' : '' }` : ''}
        ${new Date(x.start).toString().substr(0, 10)} ${new Date(x.start).toString().substr(16, 5)}<br/>
        ${x.nombre} ${x.apellidos} <a href='tel:${x.phone}'>${x.phone}</a><br/>
        ${x.type}<br/>
        ${x.deleted == 1 ? '<b>CANCELLED</b>' : '' }
      </div>
    `).join('');
    if(document.querySelector('#ss_booking_list_list').innerHTML == '') document.querySelector('#ss_booking_list_list').innerHTML = 'None';
  }
  bookingManager.hideDayControls = function(){
    document.querySelector('#ss_booking_day_controls').style.display = 'none';
  }
  bookingManager.showDayControls = function(){
    document.querySelector('#ss_booking_day_controls').style.display = 'flex';
  }
  bookingManager.clearBooking = function(){
    document.querySelector('#ss_booking_current_action').innerHTML = '';
    document.querySelector('#ss_booking_current_action').style.display = 'none';
    calendar.clearHighlight();
  }
  bookingManager.displayBooking = function(id, message){
    var booking = calendar.bookings.find(x=>x.id==id);
    if(booking){

			requestAnimationFrame(function(){
				var offset = jQuery('#ss_booking_current_action').offset(); // Contains .top and .left
				offset.top -= 500;
				jQuery('html, body').animate({
						scrollTop: offset.top,
				});
			});

      document.querySelector('#ss_booking_current_action').style.display = 'block';
      document.querySelector('#ss_booking_current_action').innerHTML = `
        <div class='closeIcon' data-action='clear-booking'></div>
				${message != undefined ? `${message}<br/>` : ''}
        <b>${booking.confirmed == 1 ? '' : 'UNCONFIRMED<br/>'}</b>
				${booking.nombre} ${booking.apellidos}<br/>
				${booking.type}<br/>
        ${booking.start.substr(11, 5)}, ${new Date(booking.start.substr(0,10)).toString().substr(0,10)}<br/>
				<a href='tel:${booking.phone}'>${booking.phone}</a><br/>
        <a href='mailto:${booking.email}'>${booking.email}</a><br/>
        <div id='ss_booking_item_actions'>
          ${booking.confirmed == 1 ? '' : `<div data-action='confirm-booking'>Confirm</div>`}
          <div data-action='change-booking'>Change${booking.confirmed == 1 ? '' : ' and Confirm'}</div>
          <div data-action='cancel-booking'>Cancel Booking</div>
					<div data-action='duplicate-booking' data-duplicate-id='${booking.id}'>Duplicate</div>
        </div>
      `;
      calendar.clearHighlight();
      calendar.highlightBooking(id);
    }
  }
  bookingManager.dialog = function(template){
    document.querySelector('#ss_booking_item_dialog').style.display = 'block';
    document.querySelector('.overlay').classList.add('on');
    document.querySelector('#ss_booking_item_dialog').innerHTML = template;
  }
  bookingManager.closeDialog = function(){
    document.querySelector('#ss_booking_item_dialog').style.display = 'none';
    document.querySelector('.overlay').classList.remove('on');
  }

  window.addEventListener('input', function(e){
    if(e.target.id=='ss_booking_list_search_box'){
      [].forEach.call(document.querySelectorAll('.ss_list_item'), function(item){
        item.style.display = 'block';
      });
      if(e.target.value != ''){
        [].forEach.call(document.querySelectorAll('.ss_list_item:not([data-name*="'+e.target.value+'"i])'), function(nonMatch){
          nonMatch.style.display = 'none';
        });
      }
    }
  });

  window.addEventListener('click', function(e){
    var t = e.target;
    if(t.dataset.hasOwnProperty('action')){
			e.preventDefault();
			if(bookingManager.mode == 'selecting'){
	      switch(t.dataset.action){
					case "show-calendar":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'block';
						document.querySelector('#ss_booking_list').style.display = 'none';
					break;
					case "show-confirmed":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'none';
						document.querySelector('#ss_booking_list').style.display = 'block';
						document.querySelector('#ss_booking_list_search_box').value = '';
						var today = new Date();
						today.setHours(0,0,0,0);
						bookingManager.showList(x=>x.confirmed==1 && x.deleted !=1 && (new Date(x.start) > today), 'confirmed');
					break;
					case "show-unconfirmed":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'none';
						document.querySelector('#ss_booking_list').style.display = 'block';
						document.querySelector('#ss_booking_list_search_box').value = '';
						var today = new Date();
						today.setHours(0,0,0,0);
						bookingManager.showList(x=>x.confirmed!=1 && x.deleted !=1 && (new Date(x.start) > today), 'unconfirmed');
					break;
					case "show-cancelled":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'none';
						document.querySelector('#ss_booking_list').style.display = 'block';
						document.querySelector('#ss_booking_list_search_box').value = '';
						bookingManager.showList(x=>x.deleted==1);
					break;
					case "show-all":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'none';
						document.querySelector('#ss_booking_list').style.display = 'block';
						document.querySelector('#ss_booking_list_search_box').value = '';
						bookingManager.showList(x=>true, 'all');
					break;
					case "show-historical":
						document.querySelector('#ss_booking_calendar_holder').style.display = 'none';
						document.querySelector('#ss_booking_list').style.display = 'block';
						document.querySelector('#ss_booking_list_search_box').value = '';
						bookingManager.showList(x=>(new Date(x.start) < new Date()), 'historical');
					break;
				}
			}
      switch(t.dataset.action){
        case "clear-booking":
          bookingManager.clearBooking();
        break;
        case "dialog-back":
          bookingManager.closeDialog();
        break;

        case "confirm-booking":
          bookingManager.dialog(`
            <b>Confirm this booking?</b><p><p>
            <input type='checkbox' id='ss_booking_email_customer'></input> <label for='ss_booking_email_customer'>Email customer</label><p><p>
            <div data-action='confirm-confirm-booking'>Confirm Booking</div> <div data-action='dialog-back'>Back</div>
          `);
        break;

				case "zoom-to-booking":
					document.querySelector('#ss_booking_calendar_holder').style.display = 'block';
					document.querySelector('#ss_booking_list').style.display = 'none';
					var date = new Date(calendar.bookings.find(x=>x.id == e.target.dataset.id).start.substr(0,10));
					calendar.drawDay(date);
					bookingManager.displayBooking(e.target.dataset.id);
				break;

        case "confirm-confirm-booking":
					bookingManager.setMode('working');
          bookingManager.closeDialog();
          jQuery.ajax({
        		type:"post", url:ajax.url, data:{
              action: 'confirm_booking',
              id: calendar.highlightedBooking.id,
							emailCustomer: document.querySelector('#ss_booking_email_customer').checked
            },
        		success: function(res){
							console.log(res);
							if(res.success){
                calendar.availability = res.data.days;
								calendar.bookings = res.data.bookings;
								calendar.drawDay(calendar.displayedDate);
								bookingManager.displayBooking(calendar.highlightedBooking.id, res.error);
								bookingManager.setMode('selecting');
							}
							else{
								bookingManager.displayBooking(calendar.highlightedBooking.id, res.error);
								bookingManager.setMode('selecting');
							}
            }
          });
        break;

				case "confirm-change-booking":
					console.log({
						action: 'change_booking',
						id: calendar.highlightedBooking.id,
						date: document.querySelector('#ss_booking_new_date').value,
						time: document.querySelector('#ss_booking_new_time').value,
						type: calendar.highlightedBooking.type,
						emailCustomer: document.querySelector('#ss_booking_email_customer').checked
					});
					jQuery.ajax({
						type:"post", url:ajax.url, data:{
							action: 'change_booking',
							id: calendar.highlightedBooking.id,
							date: document.querySelector('#ss_booking_new_date').value,
							time: document.querySelector('#ss_booking_new_time').value,
							type: calendar.highlightedBooking.type,
							emailCustomer: document.querySelector('#ss_booking_email_customer').checked
						},
						success: function(res){
							console.log(res);
							if(res.success){
								calendar.availability = res.data.days;
								calendar.bookings = res.data.bookings;
								calendar.drawDay(calendar.displayedDate);
								bookingManager.displayBooking(calendar.highlightedBooking.id, res.error);
								bookingManager.setMode('selecting');
							}
							else{
								bookingManager.setMode('selecting');
								bookingManager.displayBooking(calendar.highlightedBooking.id, res.error);
							}
						}
					});
					bookingManager.closeDialog();
					bookingManager.setMode('working');
				break;

				case "confirm-cancel-booking":
					bookingManager.setMode('working');
					bookingManager.closeDialog();
					jQuery.ajax({
						type:"post", url:ajax.url, data:{
							action: 'delete_booking',
							id: calendar.highlightedBooking.id,
							emailCustomer: document.querySelector('#ss_booking_email_customer').checked
						},
						success: function(res){
							console.log(res);
							if(res.success){
								calendar.availability = res.data.days;
								calendar.bookings = res.data.bookings;
								calendar.drawDay(calendar.displayedDate);
								bookingManager.clearBooking();
								bookingManager.setMode('selecting');
							}
							else{
								bookingManager.displayBooking(calendar.highlightedBooking.id, res.error);
								bookingManager.setMode('selecting');
							}
						}
					});
				break;

				case "accept-change-booking":
					bookingManager.dialog(`
						<b>Confirm booking with new date/time?</b><p><p>
						<input type='checkbox' id='ss_booking_email_customer'></input> <label for='ss_booking_email_customer'>Email customer</label><p><p>
						<div data-action='confirm-change-booking'>Confirm Booking</div> <div data-action='dialog-back'>Back</div>
					`);
				break;

        case "change-booking":
				 	calendar.selectType(calendar.highlightedBooking.type, calendar.highlightedBooking.length);
					bookingManager.setMode('picking', function(dateString, time){
						var booking = calendar.highlightedBooking;
						document.querySelector('#ss_booking_current_action').innerHTML = `
							<b>${booking.confirmed == 1 ? '' : 'UNCONFIRMED<br/>'}</b>
							<input type='hidden' id='ss_booking_new_date' value='${dateString}'></input>
							<input type='hidden' id='ss_booking_new_time' value='${time}'></input>
							${time} ${new Date(dateString).toString().substr(0,10)} - ${booking.type}<br/>
							${booking.nombre} ${booking.apellidos}<br/>
							<a href='tel:${booking.phone}'>${booking.phone}</a><br/>
							<a href='mailto:${booking.email}'>${booking.email}</a><br/>
							<div id='ss_booking_item_actions'>
								<div data-action='accept-change-booking'>Confirm New Date/Time</div>
								<div data-action='back-to-display-booking'>Back</div>
							</div>
						`;
					}, function(){
					});
					var booking = calendar.highlightedBooking;
					document.querySelector('#ss_booking_current_action').innerHTML = `
						<b>${booking.confirmed == 1 ? '' : 'UNCONFIRMED<br/>'}</b>
						<b>Select date/time from the calendar above.</b><br/>
						${booking.nombre} ${booking.apellidos}<br/>
						<a href='tel:${booking.phone}'>${booking.phone}</a><br/>
						<a href='mailto:${booking.email}'>${booking.email}</a><br/>
						<div id='ss_booking_item_actions'>
							<div data-action='back-to-display-booking'>Back</div>
						</div>
					`;

        break;

				case "back-to-display-booking":
					bookingManager.setMode('selecting');
					calendar.clearSelect();
					bookingManager.displayBooking(calendar.highlightedBooking.id);
				break;

        case "cancel-booking":
          bookingManager.dialog(`
            <b>Cancel this booking?</b><p><p>
            <input type='checkbox' id='ss_booking_email_customer'></input> <label for='ss_booking_email_customer'>Email customer</label><p><p>
            <div data-action='confirm-cancel-booking'>Cancel Booking</div> <div data-action='dialog-back'>Back</div>
          `);
        break;

				case "cancel-add-booking":
					bookingManager.setMode('selecting');
					calendar.clearSelect();
					document.querySelector('#ss_booking_current_action').style.display = 'none';
				break;

				case "add-new":
		      bookingManager.addNew();
				break;

				case "clear-availability-row":
					e.target.parentNode.querySelectorAll('input')[0].value = null;
					e.target.parentNode.querySelectorAll('input')[1].value = null;
				break;

				case "clear-opening-hours":
					[].forEach.call(e.target.parentNode.parentNode.querySelectorAll('input'), function(input){
						input.value = null;
					});
				break;

				case "apply-opening-hours":
					var data = [
						{start:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_start_0').value), end:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_end_0').value)},
						{start:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_start_1').value), end:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_end_1').value)},
						{start:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_start_2').value), end:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_end_2').value)},
						{start:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_start_3').value), end:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_end_3').value)},
						{start:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_start_4').value), end:bookingManager.timeMinutes(document.querySelector('#ss_booking_availability_end_4').value)},
					];
					bookingManager.setMode('working');
					jQuery.ajax({
						type:"post", url:ajax.url, data:{
							action: 'change_opening_hours',
							date: calendar.dateToMySQL(calendar.displayedDate),
							data: data
						},
						success: function(res){
							console.log(res);
							document.querySelector('#ss_booking_current_action').style.display = 'none';
							if(res.success){
								calendar.availability = res.data.days;
								bookingManager.availability = res.data.availability;
								calendar.drawDay(calendar.displayedDate);
								bookingManager.setMode('selecting');
							}
							else{
								bookingManager.setMode('selecting');
							}
						}
					});
				break;

				case "change-opening-hours":
					bookingManager.hideDayControls();
					document.querySelector('#ss_booking_current_action').style.display = 'block';
					calendar.clearHighlight();
					var date = calendar.dateToMySQL(calendar.displayedDate);
					if(bookingManager.availability[date] == undefined){
						bookingManager.availability[date] = {};
					}
					for(var i = 0; i<5; i++){
						if(bookingManager.availability[date][i] == undefined){
							bookingManager.availability[date][i] = {
								"start": null,
								"end": null
							}
						}
					}
					document.querySelector('#ss_booking_current_action').innerHTML = '<b>Opening Hours</b><br/>Leave everything blank to use default for that day.<p>' + Array(5).fill().map((x, i)=>`
						<div>
							<label for='ss_booking_availability_start_${i}'>Open</label>
							<input type='time' id='ss_booking_availability_start_${i}' min='00:00' max='24:00' step='1800' value='${bookingManager.availability[date][i]['start']}'></input>
							<label for='ss_booking_availability_end_${i}'>Close</label>
							<input type='time' id='ss_booking_availability_end_${i}' min='00:00' max='24:00' step='1800' value='${bookingManager.availability[date][i]['end']}'></input>
							<div class='clearIcon' data-action='clear-availability-row'></div>
						</div>
					`).join('')+`
						<p>
						<div id="ss_booking_item_actions">
		          <div data-action="apply-opening-hours">Apply</div>
							<div data-action="clear-opening-hours">Clear</div>
		          <div data-action="opening-hours-back">Back</div>
		        </div>
					`;
				break;

				case "opening-hours-back":
					document.querySelector('#ss_booking_current_action').style.display = 'none';
					bookingManager.showDayControls();
				break;

				case "duplicate-booking":
					document.querySelector('#ss_booking_calendar_holder').style.display = 'block';
					document.querySelector('#ss_booking_list').style.display = 'none';
					bookingManager.addNew(e.target.dataset.duplicateId);
				break;

				case "confirm-new-booking":
				var data = {
					action: 'new_booking',
					date: document.querySelector('#ss_booking_new_date').value,
					time: document.querySelector('#ss_booking_new_time').value,
					type: document.querySelector('#ss_new_type').selectedOptions[0].value,
					confirmed: true,
					name: document.querySelector('#ss_new_name').value,
					surnames: document.querySelector('#ss_new_surnames').value,
					email: document.querySelector('#ss_new_email').value,
					phone: document.querySelector('#ss_new_phone').value,
					emailCustomer: document.querySelector('#ss_booking_email_customer').checked
				};
				bookingManager.setMode('working');
					jQuery.ajax({
						type:"post", url:ajax.url, data:data,
						success: function(res){
							console.log(res);
							if(res.success){
								calendar.availability = res.data.days;
								calendar.bookings = res.data.bookings;
								bookingManager.setMode('selecting');
								var date = new Date(calendar.bookings.find(x=>x.id == res.id).start.substr(0,10));
								calendar.drawDay(date);
								calendar.drawDay(calendar.displayedDate);
								bookingManager.displayBooking(res.id);
							}
							else{
								document.querySelector('#ss_booking_current_action').style.display = 'block';
							 	document.querySelector('#ss_booking_current_action').innerHTML = `<b>${res.error}</b>`;
								bookingManager.setMode('selecting');
							}
						}
					});
				break;

      }
    }
    else if(bookingManager.mode == 'selecting' && t.nodeName == 'SPAN' && t.parentNode.classList.contains('grain') && t.parentNode.classList.contains('booking')){
      bookingManager.displayBooking(t.parentNode.dataset.id);
    }
  });


	jQuery.ajax({
		type: "post",url: ajax.url,data: { action: 'get_detailed' },
		success: function(res){
			console.log(res);
      calendar = new SabaiSaludCalendar({
        "dayStart": res.dayStart,
        "dayEnd": res.dayEnd,
        "granularity": res.granularity,
        "maxFutureDays": res.days.length,
        "startDate": res.startDate
      }, res.days, res.bookings, res.prepSlots, true);

      calendar.draw(document.querySelector('#ss_booking_calendar'));

			bookingManager.types = res.types;
			bookingManager.availability = res.availability;
			bookingManager.setMode('selecting');

			if(calendar.bookings.filter(x=>x.confirmed==0&&x.deleted==0).length>0){

			}

		}
	});
});
