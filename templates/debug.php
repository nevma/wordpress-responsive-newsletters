<!--
    Helps logged in users debug newsletters layout.
 -->

<form method ="get" 
      action = ""
      style = "position: fixed; left: 0; top: 0; padding: 1em; border-right: 1px dotted gray; border-bottom: 1px dotted gray;">

    <label for = "debug"
           style = "cursor: hand; cursor: pointer;">

        <input type = "checkbox"
               name = "debug"
               id = "debug"
               value = "true"
               <?php echo $_GET['debug'] == 'true' ? 'checked' : ''; ?>
               onchange = "this.form.submit()" />
        Debug

    </label>

    <br />

    <label for = "emogrify"
           style = "cursor: hand; cursor: pointer;">

        <input type = "checkbox"
               name = "emogrify"
               id = "emogrify"
               value = "false"
               <?php echo $_GET['emogrify'] == 'false' ? 'checked' : ''; ?>
               onchange = "this.form.submit()" />
        No emogrify

    </label>

</form>