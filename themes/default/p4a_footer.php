<!-- Removing the following section is forbidden -->
<div id="p4a_footer">
	<?php if (!$this instanceof P4A_Login_Mask): ?>
	<a href="<?php echo P4A_APPLICATION_SOURCE_DOWNLOAD_URL ?>"><?php echo __("Download application's source code") ?></a>
	<br />
	<?php endif; ?>
	Powered by <a href="http://p4a.sourceforge.net/welcome">P4A - PHP For Applications</a> <?php echo P4A_VERSION?>
</div>

<?php echo $this->maskClose() ?>
</div>
</body>
</html>