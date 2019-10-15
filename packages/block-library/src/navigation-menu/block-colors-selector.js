/**
 * External dependencies
 */
import classnames from 'classnames';
import { noop } from 'lodash';

/**
 * WordPress dependencies
 */
import { IconButton, Dropdown, Toolbar } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { DOWN } from '@wordpress/keycodes';
import { ColorPaletteControl, ContrastChecker } from '@wordpress/block-editor';

/**
 * Color Selector Icon component.
 * @return {*} React Icon component.
 * @constructor
 */
const ColorSelectorIcon = ( { style } ) =>
	<div className="block-library-colors-selector__icon-container">
		<div
			className="block-library-colors-selector__state-selection wp-block-navigation-menu-item"
			style={ style }
		>
			{ __( 'Aa' ) }
		</div>
	</div>;

/**
 * Renders the Colors Selector Toolbar with the icon button.
 *
 * @param {object} style Colors style object.
 * @param {function} onToggle Callback open/close Dropdown.
 * @param {bool} isOpen True is the color settings dropdown is open. Otherwise, False.
 * @return {*} React toggle button component.
 */
const renderToggleComponent = ( style ) => ( { onToggle, isOpen } ) => {
	const openOnArrowDown = ( event ) => {
		if ( ! isOpen && event.keyCode === DOWN ) {
			event.preventDefault();
			event.stopPropagation();
			onToggle();
		}
	};

	return (
		<Toolbar>
			<IconButton
				className="components-icon-button components-toolbar__control block-library-colors-selector__toggle"
				label={ __( 'Open Colors Selector' ) }
				onClick={ onToggle }
				onKeyDown={ openOnArrowDown }
				icon={ <ColorSelectorIcon style={ style }/> }
			/>
		</Toolbar>
	);
};

const renderContent = ( { backgroundColor, textColor, onColorChange = noop } ) => ( ( { isOpen, onToggle, onClose } ) => {
	const setColor = colorType => value => onColorChange( { colorType, value } );

	return (
		<>
			<div className="color-palette-controller-container">
				<ColorPaletteControl
					value={ backgroundColor.color }
					onChange={ setColor( 'backgroundColor' ) }
					label={ __( 'Background Color' ) }
				/>
			</div>

			<div className="color-palette-controller-container">
				<ColorPaletteControl
					value={ textColor.color }
					onChange={ setColor( 'textColor' ) }
					label={ __( 'Text Color' ) }
				/>
			</div>

			<ContrastChecker
				textColor={ textColor.color }
				backgroundColor={ backgroundColor.color }
				isLargeText={ false }
			/>
		</>
	)
} );

export default ( { style, className, ...colorControlProps } ) =>
	<Dropdown
		position="bottom right"
		className={ classnames( 'block-library-colors-selector', className ) }
		contentClassName="block-library-colors-selector__popover"
		renderToggle={ renderToggleComponent( style ) }
		renderContent={ renderContent( colorControlProps ) }
	/>;
