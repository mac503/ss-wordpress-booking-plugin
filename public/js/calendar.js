function SabaiSaludCalendar(display, availability, bookings, prepSlots = 0, displayPrepTime = false){
  this.display = display;
  this.display.startDate = new Date(this.display.startDate);
  this.display.startDate.setHours(0,0,0,0);
  this.availability = availability;
  this.bookings = bookings;
  this.prepSlots = prepSlots;
  this.displayPrepTime = displayPrepTime;

  this.mode = null;
}

SabaiSaludCalendar.prototype.draw = function(div){
  this.div = div;
  div.classList.add('sabai-salud-calendar');
  //draw the calendar (TODO use babel to make sure all browsers can deal with this)
  div.innerHTML = `
    <div class='sabai-salud-calendar-header'>
      <span class='sabai-salud-calendar-nav prev'>&#x2B05; Prev</span><span class='sabai-salud-calendar-date'></span><span class='sabai-salud-calendar-nav next'>Next &#x27A1;</span>
    </div>
    <div class='sabai-salud-calendar-body' data-display-prep-time='${this.displayPrepTime}'>
      ${Array(this.display.dayEnd-this.display.dayStart).fill().map((x,i)=>i+this.display.dayStart).map(x=>`
        <div class='hour'><div class='hour-label'>${this.pad(x)}:00</div><div class='grains-holder'>
          ${Array(60 / this.display.granularity).fill().map((y,i)=>`
            <div class='grain' data-time='${this.pad(x)+':'+this.pad(this.display.granularity*i)}' data-prep-slots='${this.prepSlots}'><span></span></div>
          `).join('')}
        </div></div>
      `).join('')}
    </div>
  `;

  var self = this;

  window.addEventListener('click', function(e){
    //navigation between dates
    if(e.target.classList.contains('sabai-salud-calendar-nav')){
      if(e.target.classList.contains('not-allowed') == false){
        calendar.drawDay(new Date(e.target.dataset.date));
      }
    }
    //setup reception of selections (if in that mode)
    if(self.mode == 'acceptSelection' && e.target.parentNode.classList.contains('grain')){
      if(e.target.parentNode.dataset.selectionAllowed == "true"){
        calendar.select(self.displayedDate, e.target.parentNode.dataset.time, self.selectedType);
      }
    }
  });
  this.drawDay(new Date(this.display.startDate));
}

SabaiSaludCalendar.prototype.selectType = function(type, length){
  this.selectedType = type;
  this.selectedLength = length;
  if(this.mode == 'acceptSelection') this.updateGrainsAllowed();
}

//modes setup
SabaiSaludCalendar.prototype.acceptSelection = function(callback, nullCallback){
  this.acceptSelectionCallback = callback;
  this.acceptSelectionNullCallback = nullCallback;
  this.mode = 'acceptSelection';
  this.div.querySelector('.sabai-salud-calendar-body').dataset.mode='accept-selection';
  this.drawDay(this.displayedDate);
}

SabaiSaludCalendar.prototype.select = function(date, time){
  if(date == null || date == undefined || time == null || time == undefined){
    if(this.acceptSelectionNullCallback) this.acceptSelectionNullCallback();
    return false;
  }
  this.selectedDate = null;
  this.selectedTime = null;
  if(this.validateSelection(date, time, this.selectedLength)){
    this.selectedDate = date;
    this.selectedTime = time;
    //get selected date in acceptable form
    var dateString = this.dateToMySQL(date);
    this.acceptSelectionCallback(dateString, time);
    this.drawSelected();
  }
  else{
    this.drawDay(this.displayedDate);
    this.acceptSelectionNullCallback();
  }
}

SabaiSaludCalendar.prototype.drawSelected = function(){
  if(this.div.querySelector('.selected')) this.div.querySelector('.selected').classList.remove('selected');
  if(this.selectedDate && this.selectedTime && this.selectedDate.toString() == this.displayedDate.toString()){
    var selectedGrain = this.div.querySelector(`.grain[data-time="${this.selectedTime}"]`);
    selectedGrain.classList.add('selected');
  }
}

SabaiSaludCalendar.prototype.validateSelection = function(date, time, length){
  var self = this;
  var startDate = new Date(this.display.startDate);
  var availability = JSON.parse(JSON.stringify(this.availability[this.daysBetween(startDate, date)]));
  //merge bookings with availability
  var excludeBookingId = -1;
  if(this.highlightedBooking) excludeBookingId = this.highlightedBooking.id;
  var dayBookings = this.bookings.filter(x=> x.start.substr(0,10) == this.dateToMySQL(date) && x.deleted!=1 && x.id != excludeBookingId);
  //find the bookings for that day
  dayBookings.forEach(function(booking){
    var bookingTimeIndex = self.indexOfTime(booking.start.substr(11));
    if(self.displayPrepTime) bookingTimeIndex -= self.prepSlots;

    var bookingSlots = parseInt(booking.length)/self.display.granularity;
    if(self.displayPrepTime) bookingSlots += self.prepSlots;

    //blank out the availability if necessary for the booking
    var i = bookingTimeIndex;
    while(i<availability.length && (i-bookingTimeIndex)<bookingSlots){
      availability[i] = 0;
      i++;
    }
  });

  //figure out the start time and the number of slots it occupies
  var timeIndex = this.indexOfTime(time);
  if(this.displayPrepTime) timeIndex -= this.prepSlots;
  var slotsRequired = Math.ceil(length / this.display.granularity);
  if(this.displayPrepTime) slotsRequired += this.prepSlots;

  //check against modified availability
  var i = timeIndex;
  while(availability[i]==1 && (i-timeIndex)<slotsRequired){
    if(i-timeIndex == slotsRequired-1) return true;
    i++;
  }

  return false;
}

SabaiSaludCalendar.prototype.updateGrainsAllowed = function(){
  //using granularity figure out how many grains a hypothetical massage with current selected length occupies
  //for each grain ensure that following x grains are free (use data)
  //if so mark it allowed, if not - disallowed
  var grains = this.div.querySelectorAll('.grain');
  var i = 0;
  while(grains[i]){
    grains[i].dataset.selectionAllowed = this.validateSelection(this.displayedDate, grains[i].dataset.time, this.selectedLength);
    if(grains[i].dataset.selectionAllowed == "true") grains[i].dataset.type = this.selectedType;
    if(grains[i].dataset.selectionAllowed == "true") grains[i].dataset.length = Math.ceil(this.selectedLength / this.display.granularity);
    if(grains[i].dataset.selectionAllowed == "true" && this.highlightedBooking){
      grains[i].dataset.name = this.highlightedBooking.nombre;
    }
    i++;
  }

  //revalidate selected slot in light of new data (and draw)
  this.select(this.selectedDate, this.selectedTime);
}

SabaiSaludCalendar.prototype.drawDay = function(date){
  //set start, max, display dates
  var startDate = new Date(this.display.startDate);
  var maxDate = new Date();
  maxDate.setHours(0,0,0,0);
  maxDate.setDate(maxDate.getDate()+this.maxFutureDays);
  if(date<startDate){
    this.drawDay(startDate);
    return;
  }
  var datePlusOne = new Date(date);
  datePlusOne.setDate(datePlusOne.getDate() + 1);
  if(datePlusOne>maxDate){
    this.drawDay(maxDate);
    return;
  }
  this.displayedDate = date;
  this.displayedDate.setHours(0,0,0,0);

  //set dates of next/prev buttons
  var temp = new Date(this.displayedDate.getTime());
  temp.setDate(temp.getDate()+1);
  this.div.querySelector('.next').dataset.date = temp;
  temp.setDate(temp.getDate()-2);
  this.div.querySelector('.prev').dataset.date = temp;

  //set "allowedness" of next/prev buttons
  if(startDate.toString() == this.displayedDate.toString()){
    this.div.querySelector('.prev').classList.add('not-allowed');
  }
  else{
    this.div.querySelector('.prev').classList.remove('not-allowed');
  }
  if(maxDate.toString() == this.displayedDate.toString()){
    this.div.querySelector('.next').classList.add('not-allowed');
  }
  else{
    this.div.querySelector('.next').classList.remove('not-allowed');
  }

  //display new date
  this.div.querySelector('.sabai-salud-calendar-date').innerHTML = this.displayedDate.toString().substr(0, 10);

  //display busy slots as busy
  var dayData = this.availability[this.daysBetween(startDate, this.displayedDate)];
  var grains = this.div.querySelectorAll('.grain');
  var i = 0;
  while(grains[i]){
    delete grains[i].dataset.name;
    grains[i].classList.remove('booking');
    grains[i].classList.remove('highlighted');
    grains[i].classList.remove('selected');
    delete grains[i].dataset.type;
    delete grains[i].dataset.length;
    if(dayData[i] == 0) grains[i].classList.add('busy');
    else grains[i].classList.remove('busy');
    i++;
  }

  //draw any bookings
  var dayBookings = this.bookings.filter(x=> x.start.substr(0,10) == this.dateToMySQL(this.displayedDate) && x.deleted != 1);
  for(var i=0; i<dayBookings.length; i++){
    var slot = this.indexOfTime(dayBookings[i].start.substr(11));
    grains[slot].classList.add('booking');
    grains[slot].dataset.id = dayBookings[i].id;
    grains[slot].dataset.name = dayBookings[i].nombre;
    grains[slot].dataset.type = dayBookings[i].type;
    grains[slot].dataset.length = Math.ceil(parseInt(dayBookings[i].length) / this.display.granularity);
    grains[slot].dataset.confirmed = dayBookings[i].confirmed;
    if(this.highlightedBooking != undefined && this.highlightedBooking == dayBookings[i]){
      grains[slot].classList.add('highlighted');
    }
  }

  //based on the currently selected massage type and its length, populate "allowed" values for each grain
  //this will also draw a selection if relevant
  if(this.mode == 'acceptSelection') this.updateGrainsAllowed();
}

////////HELPERS////////

SabaiSaludCalendar.prototype.pad = function(x){
  return ('00'+String(x)).substr(String(x).length, 2);
}

SabaiSaludCalendar.prototype.treatAsUTC = function(date) {
  var result = new Date(date);
  result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
  return result;
}

SabaiSaludCalendar.prototype.daysBetween = function(startDate, endDate) {
  var millisecondsPerDay = 24 * 60 * 60 * 1000;
  return (this.treatAsUTC(endDate) - this.treatAsUTC(startDate)) / millisecondsPerDay;
}

SabaiSaludCalendar.prototype.dateToMySQL = function(date){
  return new Date(date.getTime() - (date.getTimezoneOffset() * 60000 ))
                    .toISOString()
                    .split("T")[0];
}

SabaiSaludCalendar.prototype.indexOfTime = function(time){
  var timeDec = parseInt(time.split(':')[0]) + parseInt(time.split(':')[1])/60;
  var grainValue = this.display.granularity / 60;
  return (timeDec - this.display.dayStart) / grainValue;
}
