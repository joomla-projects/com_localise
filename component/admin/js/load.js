/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
jQuery(document).ready(function ($){


	// Declare loadProcess Object
	$.extend({ 
		loadProcess: function () {

			// Get the title element
			title = document.getElementById('title');
			// Get the status element
			migrate_status = document.getElementById('migrate_status');
			// Get the currItem element
			currItem = document.getElementById('currItem');
			// Get the totalItems element
			totalItems = document.getElementById('totalItems');


			function fireProcess() {
				return $.ajax({
					type: "GET",
					url: 'index.php?option=com_localise&format=raw&task=ajax.process',
					//data: {lang:lang},
					complete: function(response){
						console.log(response.responseText);

						var row_object = JSON.decode(response.responseText);

						if (row_object.cid == 0) {
							//$('pb4').pb4.reset();
							currItem.innerHTML = 1;
						}else{
							currItem.innerHTML = row_object.cid;
						}

						if (row_object.cid == row_object.stop.toInt()+1 || row_object.next == 1 ) {
							if (row_object.end == 1) {
								// @@ TODO: finish js
							} else if (row_object.next == 1) {
								getStep();
							}
						}

					}
				});
			}

			//
			// Request the resource
			//
			function getStep() {
				return $.ajax({
					type: "GET", 	
					url: 'index.php?option=com_localise&format=raw&task=ajax.step',
					complete: function(response) {

						var object = JSON.decode(response.responseText);

						// Changing title and statusbar
						title.innerHTML = 'Loading <b><i>'+object.client+' '+object.name+'</b></i> language';

						var count1 = object.cid / object.total;
						var percent = count1 * 100;

						if (object.middle != true) {
							if (object.cid == 0) {
								currItem.innerHTML = 1;
							}else{
								currItem.innerHTML = object.cid;
							}
						}
						totalItems.innerHTML = object.total;

						// Running the request[s]
						if (object.total != 0) {
							fireProcess();
						}

					}
				}); // end ajax
			}

			// Cleanup and run the first step call
			$.ajax({
				type: "GET",
				url: 'index.php?option=com_localise&format=raw&task=ajax.checks',
				complete: function(response){

					var row_object = JSON.decode(response.responseText);

					if (row_object.number == '500') {
						console.log('Cleanup and check DONE');
						// Get the first step
						getStep();
					}
				}
			});

		}

	});


	/*
	 *	Joomla submit
	 */
	Joomla.submitbutton = function(task)
	{
		// Run the step
		if (task == 'process') {
			$.loadProcess();
			return false;
		}

		if (typeof(form) === 'undefined') {
			form = document.getElementById('adminForm');
		}

		if (typeof(task) !== 'undefined' && task !== "") {
			form.task.value = task;
		}

		// Submit the form.
		if (typeof form.onsubmit == 'function') {
			form.onsubmit();
		}
		if (typeof form.fireEvent == "function") {
			form.fireEvent('submit');
		}
		form.submit();

	}
});
