[[if !$handheld]]
<link href="[[$tpl_path]]/calendar.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="[[$tpl_path]]/calendar_stripped.js"></script>
<script type="text/javascript" src="[[$tpl_path]]/lang/calendar-en.js"></script>
<script type="text/javascript" src="[[$tpl_path]]/calendar-setup_stripped.js"></script>
<script type="text/javascript">
  Calendar.setup(
    {
      inputField  : "[[$id]]",      // ID of the input field
      ifFormat    : "[[$date_format]]",    // the date format
      button      : "[[$id]]button"    // ID of the button
    }
  );
</script>
[[/if]]