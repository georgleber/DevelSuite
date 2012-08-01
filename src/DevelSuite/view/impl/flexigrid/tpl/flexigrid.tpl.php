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
				console.log("Searching for column name: " + columnName + ", count columns: " + this.columns.length);
				for (var i = 0; i < this.columns.length; i++) {
					if (this.columns[i].name == columnName) {
						console.log("internal column name: " + this.columns[i].name);
						return column;
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

		function getRequestedColumns(grid, requestColumns, multiSelection) {
			console.log(grid);
			var resultSet = new ResultSet(multiSelection);
			
			if ($('.trSelected', grid).length == 0) {
				jAlert("Es wurde kein Datensatz ausgewählt.", "Fehler");
				return null;
			} else if (!multiSelection && $('.trSelected', grid).length > 1) {
				jAlert("Es wurde mehr als ein Datensatz ausgewählt, es ist aber nur einfache Selektierung zulässig", "Fehler");
				return null;
			} else {
				$('.trSelected', grid).each(function(rowIndex) {
					var row = new Row(rowIndex);

					$.each(requestColumns, function(index, columnName) {
						var colIndex = getColumnIndex(columnName);
						var colIndex2 = getIndexByTitle(columnName);

						console.log("Index1: " + colIndex + ", Index2: " + colIndex2);

						if (colIndex != -1) {
							var cell = $('td:nth-Child(' + colIndex + ')', this);
							var value = $(cell).children('div').html();
	
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
	 		return $('div.hDivBox > table > thead > tr > th[title="' + title + '"]').index();
		}
					

		function getColumnIndex(columnName, grid) {
			var columnIndex = -1;
			console.log("loading column index for column: [" + columnName + "]");
			$('div.hDivBox > table > thead > tr > th', grid).each(function(index) {
				 var name = $(this).attr('title');
				 console.log("Checking cell: [" + name + "]");
				 if (name === columnName) {
					 columnIndex = index;
					 return;					 
				 }
			});

			if (columnIndex > -1) {
				console.log("found column at index: " + columnIndex);
			}

			return columnIndex;
		}
	});
/* ]]> */
</script>
<table class="grid"
	style="display: none"></table>
