jQuery(document).ready(function(){

	window.addEventListener('click', function(e){
    if(e.target.classList.contains('newType')) newMassage();
    if(e.target.classList.contains('removeType')) e.target.parentNode.parentNode.removeChild(e.target.parentNode);
  });

  jQuery('#ss_massage_types_form').submit(function(e){
    e.preventDefault();
    var types = {};
    [].forEach.call(document.querySelectorAll('#ss_massage_types_form .massageType'), function(x){
      types[x.dataset.typeId] = {
        id: x.dataset.typeId,
        name: x.querySelector('[name="name"]').value,
        displayname: x.querySelector('[name="displayname"]').value,
        length: x.querySelector('[name="length"]').value,
        allowbookings: x.querySelector('[name="allowbookings"]').checked ? 1 : 0
      }
    });
    jQuery.ajax({
         data: {action: 'update_massage_types', data: types },
         type: 'post',
         url: ajax.url,
         success: function(x){
           console.log(x);
         },
         error: function(jqXHR, exception){

         }
    });
  });

  jQuery('#ss_massage_openings_form').submit(function(e){
    e.preventDefault();
    var openings = [];
    [].forEach.call(document.querySelectorAll('#ss_massage_openings_form input:not([type="submit"])'), function(x){
      split = x.name.split('-');
      var opening = openings.find(x=>x.weekday == split[0] && x.idWithinDay == split[1]);
      if(opening == undefined){
        opening = {
          weekday: split[0],
          idWithinDay: split[1]
        }
        opening[split[2]] = (x.value.split(':')[0] * 60) + parseInt(x.value.split(':')[1]);
        openings.push(opening);
      }
      else opening[split[2]] = (x.value.split(':')[0] * 60) + parseInt(x.value.split(':')[1]);
    });
    jQuery.ajax({
         data: {action: 'update_massage_openings', data:openings },
         type: 'post',
         url: ajax.url,
         success: function(x){
           console.log(x);
         },
         error: function(jqXHR, exception){

         }
    });
  });

});

function newMassage(){
  var count = document.querySelectorAll('.massageType').length;
  var div = document.createElement('div');
  div.className='massageType';
  div.dataset.typeId = count;
  div.innerHTML = "<label>Name <input type='text' name='name'></input></label> <label>Display <input type='text' name='displayname'></input></label> <label>Mins <input type='number' min='0' max='180' name='length'></input></label> <label>Allow Bookings <input type='checkbox' checked  name='allowbookings'></input></label> <span class='removeType' style='cursor:pointer; font-size: 2em; position: relative; top: .2em;'>×</span>";
  document.querySelector('#ss_massage_types').appendChild(div);
  div.querySelector('input').focus();
}

function validateMassageNames(){

}
