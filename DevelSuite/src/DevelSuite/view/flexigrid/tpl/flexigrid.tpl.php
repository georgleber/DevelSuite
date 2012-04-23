<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
	$(document).ready(function() {
		// create a flexigrid table
		$('.grid').flexigrid({
			url: '<?php echo $this->url; ?>',
			dataType: 'json',
			colModel: [ 
				<?php for ($i = 0; $i < count($this->colModel); $i++): ?>
					{   display: '<?php echo $this->colModel[$i]["display"]; ?>',  
						name: '<?php echo $this->colModel[$i]["name"]; ?>', 
						width: <?php echo $this->colModel[$i]["width"]; ?>, 
						sortable: <?php echo $this->colModel[$i]["sortable"]; ?>, 
						align: 'center' 
					}<?php if($i != (count($this->colModel)-1)) { echo ","; } ?> 
				<?php endfor; ?>
			],
			buttons: [ 
				<?php foreach ($this->buttons as $id => $button):
						if ($id == "separator"): echo "{ separator: true },";
						else: ?>
								{ name: '<?php echo $button->getName(); ?>',
								  bclass: '<?php echo $button->getBClass(); ?>',  
								  onpress: <?php echo $button->getOnpress(); ?> 
								},
				<?php	endif; 
					endforeach; ?>
			],
			searchitems: [
				<?php for ($i = 0; $i < count($this->searchItems); $i++): ?>
					{	 display: '<?php echo $this->searchItems[$i]["display"]; ?>',  
						 name: '<?php echo $this->searchItems[$i]["name"]; ?>'
						<?php if (isset($this->searchItems[$i]["isdefault"])) { echo ", isdefault: true"; } ?>
					}<?php if($i != (count($this->searchItems)-1)) { echo ","; } ?>
				<?php endfor; ?>
			],
			<?php if ($this->options != NULL && count($this->options) > 0): ?>
				<?php foreach ($this->options as $optKey => $optVal): ?>
					<?php echo $optKey; ?>: <?php echo $optVal; ?>,
				<?php endforeach; ?>
			<?php endif; ?>
			title: '<?php echo $this->title; ?>',
			usepager: true,
			useRp: true,
			procmsg: 'Daten werden geladen, bitte warten...',
			nomsg: 'Keine Daten vorhanden',
			pagestat: 'Zeige {from} - {to} Datensätze ({total} insgesamt)',
			findtext: 'Suche:',
			pagetext: 'Seite',
			outof: 'von',
			singleSelect: <?php if ($this->singleSelect) echo "true"; else echo "false"; ?>,
			onError: function(xhr) {
				jAlert("Error occured: " + xhr.responseText);
			}
		});

		<?php foreach ($this->buttons as $id => $button): ?>
			<?php if ($id == "separator") continue; ?>
			
			function <?php echo $button->getOnpress(); ?>(com, grid) {
				<?php if ($button->needID()): ?>
					var ID = getSelectedColumnContent(grid, "ID");
					if (ID != null) {
						<?php echo $button->getCallBack(); ?>(ID);
					}
				<?php elseif ($button->needMultipleIDs()): ?>
					var IDs = getSelectedColumnContents(grid, "ID");
					if (IDs.length != 0) {
						<?php echo $button->getCallBack(); ?>(IDs);
					}
				<?php else: ?>
					<?php echo $button->getCallBack(); ?>();
				<?php endif; ?>
			}
		<?php endforeach; ?>
		
		function gridReload() {
			$('.grid').flexReload();
		}

		function getSelectedColumnContent(grid, columnID) {
			var content = null; 
			if ($('.trSelected', grid).length == 0) {
				jAlert("Es wurde kein Datensatz ausgewählt.", "Fehler");
			} else if ($('.trSelected', grid).length > 1) {
				jAlert("Es darf nur ein Datensatz ausgewählt werden.", "Fehler");
			} else { 
				$('.trSelected td', grid).each(function() {
					var abbr = $(this).attr('abbr');
					if (abbr == columnID) {
						content = $(this).children('div').html();
					}
				});
			}
			
			return content;
		}
		
		function getSelectedColumnContents(grid, columnID) {
			var content = new Array(); 
			if ($('.trSelected', grid).length == 0) {
				jAlert("Es wurde kein Datensatz ausgewählt.", "Fehler");
			} else {
				var count = 0;
				$('.trSelected td', grid).each(function() {
					var abbr = $(this).attr('abbr');
					if (abbr == columnID) {
						content[count] = $(this).children('div').html();
						count++;
					}
				});
			}
			
			return content;
		}
	});
/* ]]> */
</script>
<table class="grid"
	style="display: none"></table>
