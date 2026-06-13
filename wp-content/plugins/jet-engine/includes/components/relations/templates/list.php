<?php
// phpcs:disable
?>
<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No relations found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'id', 'name', 'post_types', 'type', 'actions' ]"
			class-name="cols-4"
			slot="heading"
		>
			<span slot="id"><?php _e( 'ID', 'jet-engine' ); ?></span>
			<span slot="name"><?php _e( 'Name', 'jet-engine' ); ?></span>
			<span slot="post_types"><?php _e( 'Related objects', 'jet-engine' ); ?></span>
			<span slot="type"><?php _e( 'Relation type', 'jet-engine' ); ?></span>
			<span slot="actions"><?php _e( 'Actions', 'jet-engine' ); ?></span>
		</cx-vui-list-table-heading>
		<cx-vui-list-table-item
			:slots="[ 'id', 'name', 'post_types', 'type', 'actions' ]"
			class-name="cols-4"
			slot="items"
			v-for="item in itemsList"
			:key="item.id"
		>
			<span slot="id">
				<span
					class="jet-engine-copy-rel-id"
					@click="copyID( item.id )"
				>
					{{ item.id }}
					<span class="jet-engine-copy-rel-id-icon">
						<svg v-if="item.id !== isIDCopied" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 8V5.2c0-1.2.02-1.49.1-1.66 .09-.19.24-.35.43-.44 .16-.09.45-.11 1.65-.11h7.6c1.19 0 1.48.02 1.65.1 .18.09.34.24.43.43 .08.16.1.45.1 1.65v7.6c0 1.19-.03 1.48-.11 1.65 -.1.18-.25.34-.44.43 -.17.08-.46.1-1.66.1h-2.8c-.56 0-1 .44-1 1 0 .55.44 1 1 1h2.8c1.6 0 1.98-.04 2.56-.33 .56-.29 1.02-.75 1.31-1.32 .29-.59.32-.97.32-2.57v-7.6c0-1.61-.04-1.99-.33-2.57 -.29-.57-.75-1.03-1.32-1.32 -.59-.3-.97-.33-2.57-.33h-7.6c-1.61 0-1.99.03-2.57.32 -.57.28-1.03.74-1.32 1.31 -.3.58-.33.96-.33 2.56v2.8c0 .55.44 1 1 1 .55 0 1-.45 1-1ZM5.2 23h7.6c1.6 0 1.98-.04 2.56-.33 .56-.29 1.02-.75 1.31-1.32 .29-.59.32-.97.32-2.57v-7.6c0-1.61-.04-1.99-.33-2.57 -.29-.57-.75-1.03-1.32-1.32 -.59-.3-.97-.33-2.57-.33h-7.6c-1.61 0-1.99.03-2.57.32 -.57.28-1.03.74-1.32 1.31 -.3.58-.33.96-.33 2.56v7.6c0 1.6.03 1.98.32 2.56 .28.56.74 1.02 1.31 1.31 .58.29.96.32 2.56.32Zm0-2c-1.2 0-1.49-.03-1.66-.11 -.19-.1-.35-.25-.44-.44 -.09-.17-.11-.46-.11-1.66v-7.6c0-1.2.02-1.49.1-1.66 .09-.19.24-.35.43-.44 .16-.09.45-.11 1.65-.11h7.6c1.19 0 1.48.02 1.65.1 .18.09.34.24.43.43 .08.16.1.45.1 1.65v7.6c0 1.19-.03 1.48-.11 1.65 -.1.18-.25.34-.44.43 -.17.08-.46.1-1.66.1h-7.6Z" fill="currentColor"/></svg>
						<svg v-if="item.id === isIDCopied" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 72"><path d="M57.658,12.643c1.854,1.201,2.384,3.678,1.183,5.532l-25.915,40c-0.682,1.051-1.815,1.723-3.064,1.814	C29.764,59.997,29.665,60,29.568,60c-1.146,0-2.241-0.491-3.003-1.358L13.514,43.807c-1.459-1.659-1.298-4.186,0.36-5.646	c1.662-1.46,4.188-1.296,5.646,0.361l9.563,10.87l23.043-35.567C53.329,11.971,55.806,11.442,57.658,12.643z" fill="#46B450"></path></svg>
					</span>
				</span>
			</span>
			<span slot="name">
				<a
					:href="getEditLink( item.id )"
					class="jet-engine-title-link"
				>{{ item.name }}</a>
				<i v-if="item.is_legacy"><small><?php _e( '(Legacy)', 'jet-engine' ); ?></small></i>
			</span>
			<i slot="post_types">{{ item.related_objects }}</i>
			<i slot="type">{{ relationsTypes[ item.args.type ] }}</i>
			<div slot="actions" style="display: flex;">
				<a :href="getEditLink( item.id )"><?php _e( 'Edit', 'jet-engine' ); ?></a>&nbsp;|&nbsp;
				<a
					href="#"
					@click.prevent="copyItem( item )"
				><?php _e( 'Copy', 'jet-engine' ); ?></a>&nbsp;|&nbsp;
				<a
					class="jet-engine-delete-item"
					href="#"
					@click.prevent="deleteItem( item )"
				><?php _e( 'Delete', 'jet-engine' ); ?></a>
			</div>
		</cx-vui-list-table-item>
	</cx-vui-list-table>
	<jet-cpt-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="deletedItem.id"
		:item-name="deletedItem.name"
	></jet-cpt-delete-dialog>
</div>
