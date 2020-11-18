window.onload = function() {
   // Checkboxs
   const checkbox_all = document.querySelector('input[type="checkbox"]#all-currencies');
   const checkbox_currencies = document.querySelectorAll('input[type="checkbox"][name="mwd_mcer_options[mwd_mcer_field_currencies][]"]');

   // Exit if no-checkboxs.
   if ( checkbox_all === null || checkbox_currencies === null ) return;

   checkbox_all.onchange = function() {
      if ( this.checked === true ) {
         checkbox_currencies.forEach( function( element ) {
            element.checked = true;
         });
      } else {
         checkbox_currencies.forEach( function( element ) {
            element.checked = false;
         });
      }
   };

   checkbox_currencies.forEach( function( element ) {
      element.onchange = function(e) {
         const cbox_currencies = document.querySelectorAll('input[type="checkbox"][name="mwd_mcer_options[mwd_mcer_field_currencies][]"]');
         
         let checked = 0;
         cbox_currencies.forEach( function( element ) {
            if ( element.checked === true ) checked++;
         });

         // If checked all checkbox_currencies, check the checkbox_all.
         if ( checked == cbox_currencies.length )
            checkbox_all.checked = true;
         else
            checkbox_all.checked = false;
      }
   });
};