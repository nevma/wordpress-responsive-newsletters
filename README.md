
# WordPress Responsive Newsletters

A WordPress plugin that helps create beautiful, robust and responsive HTML newsletters. 

## Key concepts and elements

These are the newsletter structural elements. You should mostly care about styling the underlined elements, as shown below, and of course the actual contents of the layout columns. 

```text
body >
  table#body-table >
    tr >
      td >
        table#email-table >
          tr >
            td.row >  // VERTICAL PADDINGS HERE
            ========  // HORIZONTAL PADDINGS HERE
              table(centering) >
                tr > 
                  td > 
                    table(flexible) >
                      tr > 
                        td > 
                          [if(mso) table>tr>td]
                          table.column-container(responsive) > 
                            tr >
                              td.column > // HORIZONTAL PADDINGS COME FROM SKIN
                              =========== // VERTICAL PADDINGS HERE
                                div.column-content > // OPTIONAL HORIZONTAL PADDINGS HERE
                                ==================== // OPTIONAL VERTICTAL PADDINGS HERE
                                  [            ]
                                  [  contents  ]
                                  [            ]
```

## Row special cases

These are special row options available for each row as ACF fields. 

 - **First row** `.first`
Marks a row as the first in the layout, so it can be styled accordingly.
 - **Last row** `.last`
Marks a row as the last in the layout, so it can be styled accordingly.
 - **No horizontal paddings** `.nexus`
Marks a row as having no horizontal paddings.
 - **No vertical paddings** `.compact`
Marks a row as having no vertical paddings.

## Image special cases

Naturally an image's width attribute will be tampered with automatically by the framework in order to be specifically set to the width of its column, taking into account column gutters. 

The editor can make certain exceptions to this rule with the following TinyMCE formats: 

 - **(mnltr) Natural width image**
Makes an exception for this particular image and allows it to keep the  width that has been set to it inside TinyMCE. 
 - **(mnltr) Compact width image**
Used in combination with the row `.compact` switch and automatically sets the width of the image to stretch to the full width of its column without the gutters. 

So, in order to achieve a full width compact image in a no padding nexus column, you need to mark the row as **No horizontal paddings** `.nexus` and the image as **(mnltr) Compact width image**. 

## Newsletter peculiarities

 - Images must always have a `width` attribute in order for Outlook to handle them properly. 
 - Background images must be applied to table cells (`td`s) in order for Outlook to handle them properly. 
 - Background colours must be applied to table cells (`td`s) in order for Outlook to handle them properly. 
 - Tables with attribute `align="left"` help achieve the responsiveness of the layout. 

## Spacers

Use as many rows as possible to vertically separate your design. Outlook does not respect margins and paddings nicely. Ideally use **one row for each element** you need or remember to use a **vertical spacer** `.mnltr-spacer` instead in order to be sure. If you put the spacer between elements then all the other margins and paddings between them are removed and only the spacing of the spacer is kept. You can put **more than one** spacer elements to increase the empty space. 

Elements that usually need special spacing treatment in Outlook are: 

 - Headings (bottom margin)
 - Tables (top margin)
 - ULs, OLs, LIs (top &amp; bottom margin)
 - Blockquotes (top &amp; bottom margin)

&copy; [Nevma.gr](https://nevma.gr)
