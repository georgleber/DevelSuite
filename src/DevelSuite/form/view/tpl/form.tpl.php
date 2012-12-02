<?php if ($this->callbackUrl != NULL): ?>
	<script type="text/javascript" charset="utf-8">
	/* <![CDATA[ */
		$(document).ready(function() {
			$('input[name="dsFormCancel"]', '#dsForm').click(function() {
				window.location.assign("<?php echo $this->callbackUrl; ?>");
			});
	
			$('#dsForm').ajaxForm({
				dataType: 'json',
				success: showResponse,
				error: function(a) {
					showException(a.responseText);
					console.log(a.responseText);
				}
			});
	
			function showResponse(data) {
				if (data.form.valid) {
					showInfoMessage("Die Daten wurden erfolgreich gespeichert", "Speichern erfolgreich", function() { 
						window.location.assign("<?php echo $this->callbackUrl; ?>");
					});
				} else {
					clear();
					var form = $('#dsForm');

					var prependHtml = false;
					var html = "";
						
					if (data.form.errors != null) {
						// generate html to show global error in form header
						html += "<div class='dsform-errors'>";
						html += "<p>Folgende Fehler sind aufgetreten:</p><ul>";
						
						// generate elements to show invalid elements
						$.each(data.form.errors, function(elem, msg) {
							if (elem == "form") {
								prependHtml = true;
								html += "<li>" + msg + "</li>";
							} else {
								$('input, textarea, select', "#dsForm").each(function() {
									if ($(this).attr('name') == elem) {
										$(this).addClass('error');
										$(this).parent().find('span.dsform-errorMsg').html(msg);
									}
								});
							}
						});

						html += "</ul></div>";
					}
						
					if (prependHtml) {
						form.prepend(html);
					}
				} 
			}
	
			function clear() {
				var form = $('#dsForm');
				$('input, textarea, select', form).each(function() {
					$(this).removeClass('error');
					$(this).parent().find('span.dsform-errorMsg').html('');
				});
				$('.dsform-errors', form).remove();
			}
		});
	/* ]]> */
	</script>
<?php endif; ?>
<form class='dsform' id ='<?php echo $this->id; ?>' action='<?php echo $this->action; ?>' method='<?php echo $this->method; ?>'<?php if (isset($this->enctype)) { echo " enctype=' . $this->enctype . '"; } ?>>
	<input type="hidden" name="form" value="<?php echo $this->id; ?>">
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