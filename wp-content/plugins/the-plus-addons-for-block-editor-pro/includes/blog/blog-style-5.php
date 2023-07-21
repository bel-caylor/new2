<?php 
    $link = get_field('page_link', get_the_ID());
?>
    <a class="tpgb-home-listing" href="<?php echo esc_url( $link ); ?>">
        <div class="first-wrap"> 
            <?php 
                if( get_field('image', get_the_ID()) ){ ?>
                    <img src="<?php the_field('image'); ?>" />
                <?php } 
    
                if( get_field('icon', get_the_ID()) ){ ?>
                    <i class="<?php the_field('icon'); ?>"></i>
                <?php } ?>
                
                <h1 class="tp-home-title"><?php echo get_the_title(); ?></h1>
        </div>
        <div class="second-wrap">
            <?php 
                $freepro = get_field_object( 'freepro' ); 
                $freepro_value = !empty($freepro['value']) ? $freepro['value'] : '';
                $standard = get_field_object( 'standard' );
                $standard_value = !empty($standard['value']) ? $standard['value'] : '';
			
			if((!empty($standard_value) && $standard_value!='None') || !empty($freepro_value)){ ?>
				<div class="freepro-standard-value-m"> <?php
			}
			if(!empty($freepro_value)){ ?>
				<span class="freepro freepro-<?php echo esc_attr($freepro_value); ?>"><?php echo esc_html($freepro_value); ?></span>
			<?php 
			}
						
			if(!empty($standard_value) && $standard_value!='None'){ ?>
				<span class="standard-value standard-value-<?php echo esc_attr($standard_value); ?>"><?php echo esc_html($standard_value); ?></span>	
			<?php 
			}
			if((!empty($standard_value) && $standard_value!='None') || !empty($freepro_value)){ ?>
				</div> <?php
			} ?> <i class="far fa-eye"></i></div>
    </a>