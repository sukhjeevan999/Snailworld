<?php
/**
 * Custom WP_Customize_Control subclasses.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Range slider control (used for base font size, featured post count, etc).
 */
class SnailWorld_Range_Control extends WP_Customize_Control {
	public $type = 'sw-range';
	public $input_attrs = array();

	public function render_content() {
		$attrs = wp_parse_args( $this->input_attrs, array(
			'min'  => 0,
			'max'  => 100,
			'step' => 1,
		) );
		?>
		<label>
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<span style="display:flex;align-items:center;gap:10px;">
				<input
					type="range"
					min="<?php echo esc_attr( $attrs['min'] ); ?>"
					max="<?php echo esc_attr( $attrs['max'] ); ?>"
					step="<?php echo esc_attr( $attrs['step'] ); ?>"
					value="<?php echo esc_attr( $this->value() ); ?>"
					style="flex:1;"
					oninput="this.nextElementSibling.value = this.value"
					<?php $this->link(); ?>
				/>
				<output><?php echo esc_html( $this->value() ); ?></output>
			</span>
		</label>
		<?php
	}
}

/**
 * Visual radio picker (used for font pairs).
 */
class SnailWorld_Radio_Card_Control extends WP_Customize_Control {
	public $type = 'sw-radio-card';

	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="sw-radio-card-group">
			<?php foreach ( $this->choices as $value => $option ) : ?>
				<label class="sw-radio-card">
					<input
						type="radio"
						value="<?php echo esc_attr( $value ); ?>"
						name="<?php echo esc_attr( $this->id ); ?>"
						<?php checked( $this->value(), $value ); ?>
						<?php $this->link(); ?>
					/>
					<span class="sw-radio-card-body">
						<strong style="font-family:<?php echo esc_attr( $option['heading_family'] ); ?>;">
							<?php echo esc_html( $option['label'] ); ?>
						</strong>
						<em style="font-family:<?php echo esc_attr( $option['body_family'] ); ?>;">
							<?php echo esc_html( $option['sample'] ); ?>
						</em>
					</span>
				</label>
			<?php endforeach; ?>
		</div>
		<style>
			.sw-radio-card-group { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }
			.sw-radio-card { display: flex; gap: 8px; align-items: flex-start; padding: 10px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; }
			.sw-radio-card input { margin-top: 4px; }
			.sw-radio-card-body { display: flex; flex-direction: column; gap: 2px; }
			.sw-radio-card-body em { font-style: normal; color: #666; font-size: 12px; }
		</style>
		<?php
	}
}

/**
 * Plain textarea control (AdSense ad-unit code).
 */
class SnailWorld_Textarea_Control extends WP_Customize_Control {
	public $type = 'sw-textarea';

	public function render_content() {
		?>
		<label>
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<textarea rows="5" style="width:100%;font-family:monospace;font-size:11px;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>
		<?php
	}
}
