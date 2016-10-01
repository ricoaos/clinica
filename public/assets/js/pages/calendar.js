//------------- calendar.js -------------//
$(document).ready(function() {

	//declare chart colours ( be sure are same with custom-variables.less or main.css file)
	var colours = {
		white: '#ffffff',
		dark: '#79859b',
		red : '#f68484',
		blue: '#75b9e6',
		green : '#71d398',
		yellow: '#ffcc66',
		brown: '#f78db8',
		orange : '#f4b162',
		purple : '#af91e1',
		pink : '#f78db8',
		lime : '#a8db43',
		magenta: '#eb45a7',
		teal: '#97d3c5',
		textcolor: '#5a5e63',
		gray: '#f3f5f6'
	}
	
	/* initialize the external events
		-----------------------------------------------------------------*/
	
	$('#external-events div.external-event').each(function() {
	
		// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		// it doesn't need to have a start or end
		var eventObject = {
			title: $.trim($(this).text()) // use the element's text as the event title
		};
		
		// store the Event Object in the DOM element so we can get to it later
		$(this).data('eventObject', eventObject);
		
		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 999,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
		
	});


	/* initialize the calendar
	-----------------------------------------------------------------*/
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultView: 'agendaWeek',
		buttonText: {
        	prev: '<i class="en-arrow-left8 s16"></i>',
        	next: '<i class="en-arrow-right8 s16"></i>',
        	today:'Hoje'
    	},
    	eventClick: function(calEvent, jsEvent, view) {

            /*alert('Event: ' + calEvent.title);
            alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
            alert('View: ' + view.name);
         	*/
    		
    		var profile='https://lh3.googleusercontent.com/-w6UO0Y5TSbk/AAAAAAAAAAI/AAAAAAAABPI/xRCP_10cdjs/s120-c/photo.jpg';
    		
    		switch (calEvent.title) {
				case 'Weidman Ferreira':
					profile='http://portal.hospitalanchieta.com.br/temas/padrao/img/ah04/avatar/1296080.png';
				break;
				
				case 'Ricardo Alexandre':
					profile='http://portal.hospitalanchieta.com.br/temas/padrao/img/ah04/avatar/1222235.png';
				break;
						
			default:
				break;
			}
    		$('#nome').html(calEvent.title);
    		$('#foto').attr('src', profile);
    		$('#idade').html(jsEvent.pageX);
    		$('#myModal').modal();
            // change the border color just for fun
            $(this).css('border-color', 'red');

        },
        dayClick: function(date, allDay, jsEvent, view) {

        	var mes = (date.getMonth() < 10 ? "0" : "") + (date.getMonth() + 1);
        	$('#myModal2').modal();
        	$('#viewData').html(date.getDate() +'/'+ mes +'/'+ date.getFullYear());
            /*if (allDay) {
                alert('Clicked on the entire day: ' + date);
            }else{
                alert('Clicked on the slot: ' + date);
            }

            alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

            alert('Current view: ' + view.name);*/
        },        
		editable: true,
		droppable: true, // this allows things to be dropped onto the calendar !!!
		drop: function(date) { // this function is called when something is dropped
		
			// retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');
			
			// we need to copy it, so that multiple events don't have a reference to the same object
			var copiedEventObject = $.extend({}, originalEventObject);
			
			// assign it the date that was reported
			copiedEventObject.start = date;
			
			// render the event on the calendar
			// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
			$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
			
			// is the "remove after drop" checkbox checked?
			if ($('#drop-remove').is(':checked')) {
				// if so, remove the element from the "Draggable Events" list
				$(this).remove();
			}	
		},
        events: [
 			{
				title: 'Ricardo Alexandre',
				start: new Date(y, m, d, 09, 0),
				end: new Date(y, m, d, 11, 0),
				allDay: false,
				description: 'Important backup on some servers.',
			},                 
        	{
				title: 'Cristiano Teles',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false,
				description: 'Morning meeting with all staff.',
			},
			{
				title: 'Weidman Ferreira',
				start: new Date(y, m, d, 06, 0),
				end: new Date(y, m, d, 08, 0),
				allDay: false,
				description: 'Important backup on some servers.',
			}
        ]

	});
	
	//force to reajust size on page load because full calendar some time not get right size.
	$(window).load(function(){
		$('#calendar').fullCalendar('render');
	});
	
	//------------- Sparklines -------------//
	$('#usage-sparkline').sparkline([35,46,24,56,68, 35,46,24,56,68], {
		width: '180px',
		height: '30px',
		lineColor: colours.dark,
		fillColor: false,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		lineWidth: 2
	});

	$('#cpu-sparkline').sparkline([22,78,43,32,55, 67,83,35,44,56], {
		width: '180px',
		height: '30px',
		lineColor: colours.dark,
		fillColor: false,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		lineWidth: 2
	});

	$('#ram-sparkline').sparkline([12,24,32,22,15, 17,8,23,17,14], {
		width: '180px',
		height: '30px',
		lineColor: colours.dark,
		fillColor: false,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		lineWidth: 2
	});

    //------------- Init pie charts -------------//
    //pass the variables to pie chart init function
    //first is line width, size for pie, animated time , and colours object for theming.
	initPieChart(10,40, 1500, colours);

 	
});

//Setup easy pie charts in sidebar
var initPieChart = function(lineWidth, size, animateTime, colours) {
	$(".pie-chart").easyPieChart({
        barColor: colours.dark,
        borderColor: colours.dark,
        trackColor: '#d9dde2',
        scaleColor: false,
        lineCap: 'butt',
        lineWidth: lineWidth,
        size: size,
        animate: animateTime
    });
}