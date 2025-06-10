<?
/*
Files
AT
14.11.23
*/

namespace ProcessWire;

$files = $filesConfig->files? : $page->files;

?>

<no-typo>
	<div class="files flex">
		<? foreach( $files as $file_ ): ?>
			<?
			$fileName = $file_->name;
			if(strlen($fileName)>20) $fileName = substr( $fileName, 0, 19 ). '…';
			$fileTitle = $file_->description? : $file_->name;
			?>
			<a
				href="<?=$file_->url?>"
				title="<?=$fileTitle?>"
				class="file flex"
				download
				>
				<div class="centered fileIcon">
					<? $svgSprite=(Object)[ 'symbol'=>'file', 'css'=>'X2L icon']; include '_shared/svg-sprite.php' ?>
					<p class="comment">
						<?=$file_->filesizeStr?>
						<?=$file_->ext?>
					</p>
				</div>
				<div class="quarter-padded fileTitle">
						<?=$fileTitle?>
				</div>
			</a>
		<? endforeach ?>
	</div>
</no-typo>

<? unset($filesConfig) ?>