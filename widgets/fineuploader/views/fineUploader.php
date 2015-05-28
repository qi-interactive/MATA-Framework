<?php
use yii\helpers\Html;
use yii\web\View;

?>

<div id="<?= $widget->id ?>" class="file-uploader">


	<?php 

	$templateId = 'qq-simple-thumbnails-template-'.$widget->id;

	$this->registerJs("
		$(document).ready(function() {

			var manualuploader = $('" . $widget->selector . " .fine-uploader').fineUploaderS3({
				request: {
					endpoint: 'https://s3-eu-west-1.amazonaws.com/" .  $widget->s3Bucket . "',
					accessKey: '" .  $widget->s3Key . "',
				},
				objectProperties: {
					acl: 'public-read',
					key: function(fileId) {
						var keyRetrieval = new qq.Promise(),
						filename = $('" . $widget->selector ." .fine-uploader').fineUploaderS3('getName', fileId);

						$.ajax({
							type: 'POST',
						  	url: '/mata-cms/media/s3/set-random-file-name',
						  	data: {name: filename},
						  	success: function(data) {
						  		var result = '" . $widget->s3Folder . "/' + data.key;
						  		keyRetrieval.success(result); 
						  	},
						  	error: function() { keyRetrieval.failure(); },
						  	dataType: 'json'
						});

						return keyRetrieval;						
					}
				},
				multiple: " . ($widget->options['multiple'] ? 'true' : 'false') . ",
			// Move to module settings
				validation: {
					allowedExtensions: ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'ico'],
					sizeLimit: 2000000
				},
				signature: {
					customHeaders: {'X-CSRF-Token':'" . \Yii::$app->request->getCsrfToken() . "'},
					endpoint: '/mata-cms/media/s3/signature'
				},
				showMessage: function(message) {
					if (message != 'No files to upload.') {
						alert(message); 
					} else {
						if(uploadsPending == 0)
							form.submit();
					}
				},
				uploadSuccess: {
					customHeaders: {'X-CSRF-Token':'" . \Yii::$app->request->getCsrfToken() . "'},
					endpoint: '" . $widget->uploadSuccessEndpoint . "'
				},
				template: '$templateId',
				autoUpload: true,
			}).on('complete', function(event, id, name, uploadSuccessResponse, t, c) {
				" . $widget->events['complete'] . "
				var fileItem = $(this).fineUploader('getItemByFileId', id);

				var form = $(this).parents('form').first();

				if (form.length == 0)
					console.error('Could not find form');

				$('input[name^=\'Media[" . $widget->id . "]\']').remove();

				for(var key in uploadSuccessResponse){
					var value = uploadSuccessResponse[key];
					var hidden = $('<input type=\'hidden\' name=\'Media[" . $widget->id . "][' + key + ']\' value=\'' + value +  '\' />');
					form.append(hidden);
				}


				$(fileItem).find('.delete-file').on('click', function() {
					$('" . $widget->selector . " li[qq-file-id=' + id + ']').remove();
					
					var documentId = $('input[name=\'Media[<?= $widget->Id ?>][DocumentId]\'').val();

					if (documentId == null)
						console.error('Cannot get Document Id');

					$('input[name^=\'Media[" . $widget->id . "]\']').remove();
					var form = $(this).parents('form').first();
					var hidden = $('<input type=\'hidden\' name=\'Media[" . $widget->id . "][delete]\' value=\'' + documentId +  '\' />');
					form.append(hidden);
					//$('" . $widget->selector . "').find('input#" . \yii\helpers\Html::getInputId($widget->model, $widget->attribute) . "').val('').trigger('mediaChanged');
					return false;
				});

}).on('progress', function(event, id, fileName, loaded, total) {

	$('" . $widget->selector . " .qq-upload-spinner').css({
		'opacity': 1, 
		width : ((loaded/total)*100) + '%'
	});

if($('" . $widget->selector . " .qq-upload-spinner')[0].style.width == '100%')
	$('" . $widget->selector . " .qq-upload-spinner').addClass('success');

}).on('submit', function() {
	$('" . $widget->selector . " .current-media').remove();
	$('" . $widget->selector . " .qq-upload-success').remove();
});


	setTimeout(function() {
		$('" .  $widget->selector . " .qq-upload-list').html($('" .  $widget->selector . " .current-media').html())
		$('" .  $widget->selector . " .current-media').remove();

		$('" .  $widget->selector . " .qq-upload-list a.delete-file').on('click', function() {

			var documentId = $('input[name=\'Media[" . $widget->Id . "][DocumentId]\'').val();

			if (documentId == null)
				console.error('Cannot get Document Id');

			console.log(documentId)
			$('input[name^=\'Media[" . $widget->id . "]\']').remove();
			var form = $(this).parents('form').first();

			if (form.length == 0)
				console.error('Could not find form');

			var hidden = $('<input type=\'hidden\' name=\'Media[" . $widget->id . "][delete]\' value=\'' + documentId +  '\' />');
			form.append(hidden);
			console.log(hidden)

			//$('" . $widget->selector . "').find('input#" . \yii\helpers\Html::getInputId($widget->model, $widget->attribute) . "').val('').trigger('mediaChanged');

			var id = $(this).parents('.qq-upload-success').attr('qq-file-id');
			$('" . $widget->selector . " li[qq-file-id=' + id + ']').remove();
			return false;
		});
	}, 300);
});", View::POS_READY);

?>


<!-- Fine Uploader DOM Element
	====================================================================== -->
	<?php
	$mediaValue = null;
	if ($mediaModel) {
		$mediaValue = $mediaModel->For;
	}

	?>
	<div class="fine-uploader"></div>

</div>
<!-- Fine Uploader template
	====================================================================== -->

	<script type="text/template" id="<?= $templateId ?>">
		<div class="qq-uploader-selector qq-uploader">
			<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
				<span>DROP your file here to upload</span>
			</div>
			<span class="qq-drop-processing-selector qq-drop-processing">
				<span>Processing dropped files...</span>
				<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
			</span>
			<?php
				if ($mediaModel):
				?>
				<div class="current-media">
					<li class="qq-file-id-0 qq-upload-success" qq-file-id="0">
					<div class="grid-item">
						<figure class="effect-winston">
							<div class="img-container">
								<img class="qq-thumbnail-selector" qq-server-scale src="<?= $mediaModel->URI ?>">
							</div>
							<figcaption>
								<p>
									<a href="#" class="delete-file"><span></span></a>
								</p>
							</figcaption>           
						</figure>
					</div>
					</li>
				</div>
				<?php
				endif;
				?>
			<ul class="qq-upload-list-selector qq-upload-list">
				<li>
					<div class="qq-progress-bar-container-selector">
						<div class="qq-progress-bar-selector qq-progress-bar"></div>
					</div>
					<span class="qq-upload-spinner-selector qq-upload-spinner"></span>

					<div class="grid-item">
						<figure class="effect-winston">
							<div class="img-container">
								<img class="qq-thumbnail-selector" qq-server-scale>
							</div>
							<figcaption>
								<p>
									<a href="#" class="delete-file"><span></span></a>
								</p>
							</figcaption>           
						</figure>
					</div>
					<!--
					<span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
					<span class="qq-upload-file-selector qq-upload-file"></span>
					<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
					<span class="qq-upload-size-selector qq-upload-size"></span>
					<a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Cancel</a>
					<a class="qq-upload-retry-selector qq-upload-retry" href="#">Retry</a>
					<a class="qq-upload-delete-selector qq-upload-delete" href="#">Delete</a>
					<span class="qq-upload-status-text-selector qq-upload-status-text"></span>
					-->
				</li>
			</ul>
			<div class="qq-upload-button-selector qq-upload-button">
				<div class="add-media-inner-wrapper">
					<div class="hi-icon-effect-2">
						<div class="hi-icon hi-icon-cog"></div>
					</div>
					<span> CLICK or DRAG & DROP </br> to upload a file</span>
				</div>
			</div>

			<?php if ($mediaValue): ?>
				<input type="hidden" name="Media[<?= $widget->Id ?>][DocumentId]" id="<?php echo \yii\helpers\Html::getInputId($widget->model, $widget->attribute) ?>" value="<?= $mediaValue ?>">
			<?php endif; ?>
		</div>
</script>
