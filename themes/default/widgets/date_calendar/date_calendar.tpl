<link href="[[$tpl_path]]/calendar.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="[[$tpl_path]]/calendar.js"></script>
<script type="text/javascript" src="[[$tpl_path]]/lang/calendar-[[$language]].js"></script>
<script type="text/javascript" src="[[$tpl_path]]/calendar-setup.js"></script>
<script type="text/javascript">
  Calendar.setup(
    {
      inputField  : "[[$id]]",      // ID of the input field
      ifFormat    : "[[$date_format]]",    // the date format
      button      : "[[$id]]button"    // ID of the button
    }
  );
</script>