<?php if (isset($this->callbackUrl)): ?>
	<script type="text/javascript" charset="utf-8">
	/* <![CDATA[ */
		$(document).ready(function() {
			$('#dsFormCancel', '#dsForm').click(function() {
				window.location.assign("<?php echo $this->callbackUrl; ?>");
			});
	
			$('#dsForm').ajaxForm({
				dataType: 'json',
				success: showResponse,
				error: function(a) {
					jException("Bei der Kommunikation mit dem Server ist ein Fehler aufgetreten.<br/>"
							+ "Bitte informieren Sie den Systemadministrator", "Exception", a.responseText);
					console.log(a.responseText);
				}
			});
	
			function showResponse(data) {
				if (data.form.valid) {					
					jAlert("Speichern erfolgreich", "Speichern erfolgreich", function() {
					window.location.assign("<?php echo $this->callbackUrl; ?>");
					});
				} else {
					clear();
					var form = $('#dsForm');
					var html = "";
						
					if (data.form.globalError != null) {
						// generate html to show global error in form header
						html += "<div class='dsform-errors'>";
						html += "<p>Folgender Fehler ist aufgetreten:</p>";
						html += "<ul><li>" + data.form.globalError + "</li></ul>";
					} else {
						// generate html to show all element errors in form header
						html += "<div class='dsform-errors'>";
						html += "<p>Folgende Fehler sind aufgetreten:</p><ul>";
						$.each(data.form.validationErrors, function() {
							html += "<li>" + this + "</li>";
						});
						html += "</ul></div>";
	
							// generate elements to show invalid elements
						$.each(data.form.validationErrors, function(elem, msg) {
							var div = null;
							if($("#" + elem).hasClass('dsform-type-radiogrp') 
									|| $("#" + elem).hasClass('dsform-type-chkgrp')
									|| $("#" + elem).hasClass('dsform-type-select')) {
								div = $("#" + elem);
							} else {
							    div = $("#" + elem).parent();
							}
	 							
							var errorMsg = "<strong class='dsform-message'>" + msg + "</strong>";
							$('#' + elem).addClass("error");
							div.prepend(errorMsg);
						});
					}
						
					form.prepend(html);
				} 
			}
	
			function clear() {
				var form = $('#dsForm');
				form.each(function() {
					$('input,textarea', this).value = '';
					$('input,textarea,select,div', this).removeClass("error");
				});
	
				$('.dsform-errors', form).remove();
				$('.dsform-message', form).remove();
			}
		});
	/* ]]> */
	</script>
<?php endif; ?>
<form class='dsform' id ='<?php echo $this->id; ?>' action='<?php echo $this->action; ?>' method='<?php echo $this->method; ?>'<?php if (isset($this->enctype)) { echo " enctype=' . $this->enctype . '"; } ?>>
	<?php if (isset($this->errorMessages) && !empty($this->errorMessages)): ?>
		<div class='dsform-errors'>
			<?php if (count($this->errorMessages) > 1): ?>
				<p>Folgende Fehler sind aufgetreten:</p>
			<?php else: ?>
				<p>Folgender Fehler ist aufgetreten:</p>
			<?php endif;?>
			<ul>
				<?php foreach ($this->errorMessages as $errorMessage): ?>
					<li><?php echo $errorMessage ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<?php if ($this->showMandatory): ?>
		<p class='dsform-mandatory'>Alle Felder mit einem <em>*</em> sind Pflichtfelder</p>
	<?php endif; ?>
	
	<!-- form elements -->
	<fieldset>
		<ul>
			<?php foreach($this->elementList as $element): ?>
				<?php if ($element instanceof DevelSuite\form\element\impl\dsDynamicContent): ?>
					<?php echo $element->buildHTML(); ?>
				<?php else: ?>
					<li class="dsform-formRow">
						<?php echo $element->buildHTML(); ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>		
		</ul>
	</fieldset>
		
	<!-- form buttons -->
	<?php if (count($this->buttonList) > 0): ?>
		<div class="dsform-buttons">
			<?php foreach ($this->buttonList as $key => $button): ?>
				<?php echo $button->getHtml(); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</form>