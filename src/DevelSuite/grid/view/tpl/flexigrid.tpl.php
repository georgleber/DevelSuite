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
							echo $column->printSearchColumn() . ",";
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

		function Column(name, value) {
			this.name = name;
			this.value = value;
		}

		function Row(index) {
			this.columns = new Array();
			this.index = index;

			this.addColumn = function(column) {
				this.columns.push(column);
			}

			this.getColumn = function(columnName) {
				for (var i = 0; i < this.columns.length; i++) {
					if (this.columns[i].name == columnName) {
						return this.columns[i];
					}
				}
			}
		}

		function ResultSet(multiSelection) {
			this.rows = new Array();
			this.multiSelection = multiSelection;
			
			this.addRow = function(row) {
				this.rows.push(row);
			}
		}

		function SelectionException(message) {
			this.message = message;
		    this.name = "SelectionException";
		}

		function getRequestedColumns(grid, requestColumns, multiSelection) {
			var resultSet = new ResultSet(multiSelection);
			
			if ($('.trSelected', grid).length == 0) {
				throw new SelectionException("Es wurde kein Datensatz ausgewählt.");
				return null;
			} else if (!multiSelection && $('.trSelected', grid).length > 1) {
				throw new SelectionException("Es wurde mehr als ein Datensatz ausgewählt, es ist aber nur einfache Selektierung zulässig");
				return null;
			} else {
				$('.trSelected', grid).each(function(rowIndex) {
					var row = new Row(rowIndex);
					var tableRow = $(this);

					$.each(requestColumns, function(index, columnName) {
						var colIndex = getIndexByTitle(columnName);

						if (colIndex != -1) {
							var value = tableRow.children().eq(colIndex).children('div').html();
							var column = new Column(columnName, value);
							row.addColumn(column);
						}
					});

					resultSet.addRow(row);
				});

				return resultSet;
			}
		}

		function getIndexByTitle(title) {
	 		var index = $('div.hDivBox > table > thead > tr > th[title="' + title + '"]').index();
	 		return index;
		}
	});
/* ]]> */
</script>
<table class="grid"
	style="display: none"></table>