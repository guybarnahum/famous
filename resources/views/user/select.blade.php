

<div id='selected-user-div'></div>

<script>

/*
setAjaxById(
     ids ,  // id
    'user/{{ $us->selected_uid }}' ,  // route
    'selected-user-div'            ); // div_id
*/

onreadyAjax( 'user/{{ $us->selected_uid }}', 'selected-user-div' );

</script>

