{def
	$class_list = $class_attribute.content.selected_class_list
	$class = false()
}

<div class="block">
    <label>{'Allowed classes'|i18n( 'design/standard/class/datatype' )}:</label>
    {if $class_list|count|gt( 0 )}
    <ul>
    	{foreach $class_list as $class_id}
    		{if eq( $class_id, 0 )}
    			<li>{'Custom tags'|i18n( 'extension/nxc_world_map' )}</li>
    		{else}
    			{set $class = fetch( 'content', 'class', hash( 'class_id', $class_id ) )}
    			<li>{$class.name}</li>
   			{/if}
    	{/foreach}
    </ul>
    {/if}
</div>