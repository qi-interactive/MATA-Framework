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
						var filename = $('" . $widget->selector ." .fine-uploader').fineUploaderS3('getName', fileId);
						return '" . $widget->s3Folder . "/' + filename;
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
			}).on('allComplete', function() {
			// setTimeout(function() {
			// 	if(uploadsPending == 0)
			// 		form.submit(); 
			// 	// form.submit();	
			// }, 800)

			}).on('complete', function(a, id, name, uploadSuccessResponse, t, c) {
				if (uploadSuccessResponse.Id == null) {
					alert('Media Upload failed. Please get in touch with your support team.');
				}
				" . $widget->events['complete'] . "
			}).on('progress', function(event, id, fileName, loaded, total) {

				$('.qq-upload-spinner').css({
					'opacity': 1, 
					width : ((loaded/total)*100) + '%'
				});

}).on('submit', function() {
	$('" . $widget->selector . " .current-media').remove();
	$('" . $widget->selector . " .qq-upload-success').remove();
});

		// form.on('submit.manualUploader', function() {
		// 	$('#" .  $widget->selector . " #current-media').remove();
		// 	manualuploader.fineUploader('uploadStoredFiles');
		// 	form.off('submit.manualUploader');
		// 	return false;
		// })
});", View::POS_READY);

?>


<!-- Fine Uploader DOM Element
	====================================================================== -->
	<?php
	$mediaValue = '';
	if ($mediaModel): ?>
	<?php 
	$mediaValue = $mediaModel->DocumentId;
	echo Html::img($mediaModel->URI, array(
		"style" => "width: 100px",
		"class" => "current-media"
		)); ?>
	<?php endif; ?>
	<div class="fine-uploader"></div>

</div>
<!-- Fine Uploader template
	====================================================================== -->

	<script type="text/template" id="<?= $templateId ?>">
		<div class="qq-uploader-selector qq-uploader">


			<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
				<span>DROP your file here to upload</span>
			</div>

			<div class="qq-upload-button-selector qq-upload-button">
				<div class="add-media-inner-wrapper"> <div class="hi-icon-effect-2">
					<div class="hi-icon hi-icon-cog"></div>
				</div> <span> CLICK or DRAG & DROP </br> to upload a file</span>
			</div>
		</div>

		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<ul class="qq-upload-list-selector qq-upload-list">
			<li>
				<div class="qq-progress-bar-container-selector">
					<div class="qq-progress-bar-selector qq-progress-bar"></div>
				</div>
				<span class="qq-upload-spinner-selector qq-upload-spinner"></span>
				<img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
				<span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
				<span class="qq-upload-file-selector qq-upload-file"></span>
				<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				<span class="qq-upload-size-selector qq-upload-size"></span>
				<a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Cancel</a>
				<a class="qq-upload-retry-selector qq-upload-retry" href="#">Retry</a>
				<a class="qq-upload-delete-selector qq-upload-delete" href="#">Delete</a>
				<span class="qq-upload-status-text-selector qq-upload-status-text"></span>
			</li>
		</ul>
		<input type="hidden" name="Media[]" id="<?php echo \yii\helpers\Html::getInputId($widget->model, $widget->attribute) ?>" value="<?= $mediaValue ?>">
	</div>
</script>