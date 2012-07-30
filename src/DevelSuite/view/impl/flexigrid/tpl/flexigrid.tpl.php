<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
	$(document).ready(function() {
		// create a flexigrid table
		$('.grid').flexigrid({
			title: '<?php echo $this->title; ?>',
			url: '<?php echo $this->url; ?>',
			dataType: '<?php echo $this->dataType; ?>',
			colModel: [ 
				<?php foreach($this->columnModel as $column): ?>
					<?php echo $column; ?>,
				<?php endforeach; ?>
			],
			buttons: [ 
				<?php foreach ($this->actions as $action): ?>
					<?php echo $action; ?>,
				<?php endforeach; ?>
			],
			searchitems: [
				<?php 
					foreach ($this->columnModel as $column):
						if ($column->isSearchable()) {
							echo $column->printSearchColumn();
						}
					endforeach; 
				?>			  		
			],
			sortname: '<?php echo $this->sortname; ?>',
			sortorder: '<?php echo $this->sortorder; ?>',
			height: <?php echo $this->height; ?>,
			usepager: true,
			useRp: true,
			rp: <?php echo $this->rp; ?>,
			errormsg: 'Verbindungsfehler',
			pagestat: 'Zeige {from} - {to} Datensätze ({total} insgesamt)',
			pagetext: 'Seite',
			outof: 'von',
			findtext: 'Suche:',
			procmsg: 'Daten werden geladen, bitte warten...',
			nomsg: 'Keine Daten vorhanden',
			singleSelect: <?php if ($this->singleSelect) echo "true"; else echo "false"; ?>,
			onError: function(xhr) {
				jAlert("Error occured: " + xhr.responseText);
			}
		});

		<?php foreach ($this->actions as $action): ?>
			<?php echo $action->getJSFunction(); ?>
		<?php endforeach; ?>
		
		function gridReload() {
			$('.grid').flexReload();
		}

		function getRequestedContent(grid, requestColumns, singleSelect) {
			var content = new Array(); 
			if ($('.trSelected', grid).length == 0) {
				jAlert("Es wurde kein Datensatz ausgewählt.", "Fehler");
				return null;
			} else if (singleSelect && $('.trSelected', grid).length > 1) {
				jAlert("Es wurde mehr als ein Datensatz ausgewählt, es ist aber nur einfache Selektierung zulässig", "Fehler");
				return null;
			} else {
				$('.trSelected', grid).each(function(row) {
					if (!singleSelect) {
						content[row] = new Array();
					}
					
					$('td', this).each(function() {
						var abbr = $(this).attr('abbr');
						var data = $(this).children('div').html();

						$.each(requestColumns, function(index, column) {
							if (abbr == column) {
								if (singleSelect) {
									content.push({name: column, value: data});
								} else {
									content[row].push({name: column, value: data});
								}	
							}
						});
					});
				});

				return content;
			}
		}
	});
/* ]]> */
</script>
<table class="grid"
	style="display: none"></table>
