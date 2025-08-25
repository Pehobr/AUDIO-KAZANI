<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package MinimalistBlogger
 */

?>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'minimalistblogger' ) ); ?>">
				<?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'minimalistblogger' ), 'WordPress' );
				?>
			</a>
			<span class="sep"> | </span>
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'minimalistblogger' ), 'minimalistblogger', '<a href="https://superbthemes.com/">SuperbThemes</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->

    <!-- Mobilní spodní navigační lišta -->
    <div class="mobile-bottom-nav">
        <a href="/" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </a>
        <a href="/kazani-podcast/" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
        </a>
        <a href="/kazani-pdf/" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </a>
        <a href="/info/" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
    </div>
</div><!-- #page -->

<?php wp_footer(); ?>

<style>
/* Styly pro mobilní spodní lištu */
.mobile-bottom-nav {
    display: none; /* Ve výchozím stavu (na PC) je lišta skrytá */
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background-color: #514332;
    color: white;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
    /* VRÁCENÝ STYL: Horní ohraničení */
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.mobile-bottom-nav .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    flex-grow: 1;
    height: 100%;
    transition: background-color 0.2s ease;
}

/* Sjednocení velikosti ikon */
.mobile-bottom-nav .nav-item svg {
    width: 24px;
    height: 24px;
}

.mobile-bottom-nav .nav-item:hover {
    background-color: #6a5a48;
}

/* Zobrazení lišty pouze na mobilních zařízeních (do šířky 768px) */
@media (max-width: 768px) {
    .mobile-bottom-nav {
        display: flex; /* Zobrazí lištu na mobilu */
    }
    body {
        padding-bottom: 60px; /* Přidá spodní odsazení, aby obsah nebyl překryt lištou */
    }
}
</style>

</body>
</html>
