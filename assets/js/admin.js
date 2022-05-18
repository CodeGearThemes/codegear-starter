window.plugin = window.plugin || {};


(function(){
	'use strict';
	
	plugin.Import = function(){

		const output = document.getElementById('block-import-output');
		document.querySelectorAll('.button-import').forEach( item => {
			item.addEventListener('click', event => {
				event.preventDefault();
				let demo_id = item.dataset.id;

				document.body.classList.add('js-import-theme-active');
				output.classList.add( 'block-import-load' );

				const data = new FormData();
				data.append( 'action', 'codegear_import_theme');
				data.append( 'nonce', Codegear_Starter_localize.nonce );
				data.append( 'demo_id', demo_id);

				fetch(Codegear_Starter_localize.ajax_url, {
					method: "POST",
					credentials: 'same-origin',
					body: data
				})
				.then((response) => response.json())
				.then((data) => {
					output.classList.remove( 'block-import-load' );
					if (data) {
						output.innerHTML = data.data;
					}

					if( data.success ){
						document.querySelector('.starter-demo-import-close').addEventListener('click', (event) => {
							event.preventDefault();
							document.body.classList.remove('js-import-theme-active');
							output.innerHTML = '';
						});
					}
				})
				.catch((error) => {
					output.innerHTML = '<span>'+Codegear_Starter_localize.failed_message+'</span>';
				});

			})
		})

		document.querySelector('.starter-demo-import-start').addEventListener('click', () => {

			document.querySelector('.block-import-start').classList.remove('block-import-active');
			document.querySelector('.block-import-process').classList.add('block-import-active');

			document.querySelector('.block-import-label').innerHTML = '0%';
			document.querySelector('.block--import-progress-indicator').style.setProperty('--app-progress', '0%');
			
			plugin.StartImport();

		});

	}

	plugin.StartImport = function( step = 0 ){

		var label = '';
		let formElements = document.querySelectorAll('.block--form-step');
		let formElement = document.querySelectorAll('.block--form-step')[step];
		let formLength = formElements.length;
		
		if( step >= formLength ){
			setTimeout(function(){
				document.querySelector('.block-import-process').classList.remove('block-import-active');
				document.querySelector('.block--import-finish').classList.add('block-import-active');
			}, 200 );

			return;

		}

		var formData = new FormData(formElement)
		for(let [name, value] of formData) {
			if( name == 'step_name' ){
				var label = value;
			}
		}

		if( formElement.querySelector('.starter-checkbox').checked ){	

			document.querySelector('.block--import-progress-label').innerHTML = label;

			fetch(Codegear_Starter_localize.ajax_url, {
				method: "POST",
				credentials: 'same-origin',
				body: formData
			})
			.then((response) => response.json())
			.then((data) => {

				if (data.success) {
					
					if ( 'undefined' !== typeof data.status && 'newAJAX' === data.status ) {
						console.log( 'First:'+step );
						plugin.StartImport( step );
					}else{
						step = step+1;
						if( step <= formLength ){
							var indicator = Math.floor( 100/formLength * step);
							document.querySelector('.block--import-progress-indicator').style.setProperty('--app-progress', indicator+'%');
							document.querySelector('.block-import-label').innerHTML = indicator+'%';
						}

						plugin.StartImport( step );
					}

				}else{
					if( document.getElementById('message_output') )
						document.getElementById('message_output').innerHTML = '<span>'+Codegear_Starter_localize.failed_message+'</span>';
				}

			})
			.catch((error) => {
				if( document.getElementById('message_output') )
						document.getElementById('message_output').innerHTML = '<span>'+Codegear_Starter_localize.failed_message+'</span>';
			});

		}else{
			plugin.StartImport( step + 1 );
		}

	}


	/*============================================================================
    Things that require DOM to be ready
	==============================================================================*/
	function DOMready(callback) {
		if (document.readyState != 'loading') callback();
		else document.addEventListener('DOMContentLoaded', callback);
	}

	plugin.init = function(){
		plugin.Import();
	}

	DOMready(function(){
		plugin.init();
	});

})();
