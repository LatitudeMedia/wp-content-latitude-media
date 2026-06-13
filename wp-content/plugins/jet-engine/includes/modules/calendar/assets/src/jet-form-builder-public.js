import JetEngineRenderAdvancedDateField from 'field-ui/advanced-date-field-render';
import createTemplateEngine from 'field-ui/template-factory';

const {
	addFilter
} = window.JetPlugins.hooks;

const {
	InputData,
} = JetFormBuilderAbstract;

function JetEngineAdvancedDate() {

	InputData.call( this );

	this.isSupported = function ( node ) {
		return node.classList.contains( 'jet-form-builder-advanced-date__input' );
	};

	this.addListeners = function () {

		const [ node ] = this.nodes;

		node.addEventListener( 'blur', event => {
			this.value.current = event.target.value;
		} );

		jQuery( node ).on( 'change', ( e, tinycolor ) => {
			this.value.current = tinycolor?.toString() ?? '';
		} );
	};

	this.setNode = function ( node ) {

		InputData.prototype.setNode.call( this, node );

		this.$input = jQuery( node );

		this.listenFormPageChange = false;

		if ( ! this.listenFormPageChange ) {
			jQuery( window).on( 'jet-form-builder/switch-page', () => {
				// this.initDateField();
			} );
			this.listenFormPageChange = true;
		}

		this.initDateField();
	};

	this.initDateField = function() {

		let labels = {};

		if ( this.$input.data( 'labels' ) ) {
			labels = this.$input.data( 'labels' );
		}

		new JetEngineRenderAdvancedDateField(
			this.$input.closest( '.jfb-advanced-date' ),
			{
				labels: labels,
				templateEngine: createTemplateEngine()
			}
		);
	};

	this.setValue = function( newValue ) {
		newValue = newValue || null;
		this.calcValue     = newValue;
		this.value.current = newValue;
	};
}

JetEngineAdvancedDate.prototype = Object.create( InputData.prototype );

addFilter(
	'jet.fb.inputs',
	'jet-form-builder/signature-field',
	function ( inputs ) {
		inputs = [ JetEngineAdvancedDate, ...inputs ];

		return inputs;
	},
);