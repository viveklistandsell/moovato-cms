<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

/**
 * Floating "Back to Top" button. Hidden until the page is scrolled past a
 * threshold, then fades in (bottom-right). Clicking smooth-scrolls to the top.
 * Styling lives in resources/css/gs.css (.back-to-top) and uses theme tokens.
 */
const visible = ref(false);
const launching = ref(false);
let launchTimer: ReturnType<typeof setTimeout> | undefined;

function onScroll(): void {
    visible.value = window.scrollY > 300;
}

function scrollToTop(): void {
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;

    if (!reduceMotion) {
        clearTimeout(launchTimer);
        launching.value = false;
        requestAnimationFrame(() => {
            launching.value = true;
        });
        launchTimer = setTimeout(() => {
            launching.value = false;
        }, 950);
    }

    window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    clearTimeout(launchTimer);
});
</script>

<template>
    <button
        type="button"
        class="back-to-top"
        :class="{ 'is-visible': visible, 'is-launching': launching }"
        aria-label="Back to top"
        @click="scrollToTop"
    >
        <span class="back-to-top__lines" aria-hidden="true">
            <i></i><i></i><i></i>
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 3465 5197.5" style="enable-background:new 0 0 3465 5197.5;" xml:space="preserve">
<g id="Bg">
	<rect style="opacity:0;fill:#FFFFFF;" width="3465" height="5197.5"/>
</g>
<g id="Ñëîé_1">
	<g>
		<path style="opacity:0.24;fill:#000301;" d="M2484.551,2221.321c5.804-0.003,10.549-4.33,11.314-9.923l36.629-7.982    c6.595-1.463,11.295-7.25,11.291-14.009v-0.011l0,0l-0.012-23.248c0-0.974-0.109-1.948-0.326-2.867    c-1.085-5.459-5.357-9.892-10.981-11.076l-36.639-7.941c-0.771-5.593-5.52-9.913-11.323-9.91l-4.275,0.002v-31.685v-100.098    v-13.397h-0.741v-0.065v-80.809v-2.745h-47.656h-49.361h-68.007v-29.501c1.069-0.45,2.168-0.814,3.227-1.294l1.045-0.418    c28.38-12.786,50.743-34.763,64.323-61.487c41.91-0.201,75.129-35.503,72.706-77.392l-17.126-295.95h0.011l-26.448-76.866    c0.954-2.226,1.857-4.472,2.982-6.633l0.208-0.417c7.998-15.377,12.562-32.223,13.525-49.479c0.002-0.042,0.011-0.083,0.014-0.125    c0.14-2.231,0.208-4.461,0.208-6.761v-88.849v-0.079l7.84,4.266l1.021,0.563l29.368,15.971l0.029,0.015    c0,0.004-0.002,0.008-0.002,0.008l51.78,28.229c0.846,0.467,1.717,0.798,2.595,1.008c2.79,0.863,5.687,0.519,8.068-0.82    c3.103-1.709,5.354-5.002,5.429-8.985l0.257-13.714l0.6-33.44c0.063-1.169,0.001-2.358-0.265-3.516    c-2.421-11.801-7.347-23.105-13.932-33.899c-2.314-3.792-4.832-7.553-7.519-11.08c-7.752-10.464-16.875-20.175-26.466-29.046    c-10.33-9.488-21.166-17.985-31.519-25.44c15.309-21.517,27.127-51.406,33.117-90.935    c29.966-197.714-87.126-268.241-140.441-350.918c0.018-0.008,0.037-0.011,0.054-0.019c-2.52-3.883-4.837-7.794-7.062-11.727    c-0.682-1.225-1.357-2.452-2.008-3.684c-1.304-2.432-2.469-4.904-3.639-7.374c-0.576-1.234-1.206-2.454-1.747-3.699    c-0.241-0.557-0.604-1.041-0.863-1.588c-1.037-2.281-2.245-4.46-3.573-6.566c-0.12-0.184-0.226-0.378-0.349-0.561    c-13.922-21.517-42.771-34.073-78.806-41.804l0.102,0.698c-105.601-22.614-272.562-4.619-307.172-51.574    c-8.618-11.745-19.459-20.783-31.407-27.676c0.866,0.094,1.682,0.158,2.551,0.254c10.116,1.736,20.086-3.736,22.311-12.501    c2.225-8.765-4.575-17.589-15.237-19.42c-90.838-15.577-184.796-15.579-275.635,0.001c-10.662,1.83-17.466,10.653-15.241,19.418    c2.224,8.765,12.191,14.235,22.304,12.499c0.861-0.095,1.669-0.157,2.526-0.251c-11.948,6.893-22.788,15.931-31.405,27.676    c-34.564,47.039-201.953,28.92-307.528,51.639c-40.277,8.641-71.636,23.274-83.135,49.756    c-4.041,9.338-9.128,18.327-14.912,27.247c-53.52,82.37-169.827,153.031-139.932,350.174c5.991,39.524,17.809,69.413,33.12,90.929    c-10.356,7.456-21.194,15.955-31.527,25.445c-9.591,8.871-18.714,18.581-26.465,29.046c-2.687,3.526-5.205,7.288-7.52,11.08    c-6.585,10.794-11.511,22.098-13.932,33.899c-0.266,1.158-0.329,2.347-0.265,3.516l0.601,33.44l0.255,13.714    c0.076,3.983,2.329,7.276,5.432,8.985c2.38,1.339,5.277,1.684,8.068,0.82c0.877-0.21,1.749-0.541,2.595-1.008l51.781-28.226    c0,0-0.003-0.007-0.005-0.011l0.029-0.015l29.369-15.971l1.02-0.563l8.089-4.401v0.214v88.849c0,2.3,0.07,4.53,0.208,6.761    c0.906,17.351,5.505,34.215,13.52,49.616l0.208,0.418c1.072,2.042,1.965,4.154,2.878,6.257l-26.573,77.23h0.012l-17.127,295.95    c-2.427,41.954,30.897,77.308,72.896,77.402c1.079,2.127,2.197,4.233,3.385,6.297c13.797,23.902,34.844,43.414,60.976,55.191    l1.045,0.417c0.985,0.445,2.007,0.782,3,1.202v29.581h-68.748h-94.194h-2.748v47.654v49.362v100.098v31.685l-4.349-0.002    c-5.804-0.003-10.554,4.317-11.324,9.91l-36.638,7.941c-5.623,1.184-9.896,5.617-10.982,11.076    c-0.216,0.919-0.324,1.893-0.326,2.867l-0.012,23.248l0,0v0.011c-0.005,6.759,4.696,12.546,11.291,14.009l36.63,7.982    c0.764,5.593,5.509,9.92,11.311,9.923l4.398,0.002v2195.565l-4.349-0.002c-5.804-0.003-10.554,4.317-11.324,9.909l-36.638,7.941    c-5.623,1.185-9.896,5.617-10.982,11.076c-0.216,0.918-0.324,1.893-0.326,2.867l-0.012,23.248l0,0v0.012    c-0.005,6.758,4.696,12.545,11.291,14.008l36.63,7.982c0.764,5.593,5.509,9.92,11.311,9.923l4.398,0.002v0.001h-0.004v37.286    l-5.523,35.128l-13.807,87.861h-21.99v54.458h23.814c4.785,19.051,14.053,37.447,27.613,54.421l1.133,1.385    c39.791,49.759,112.074,82.36,195.797,88.917c-0.1,0.364-0.1,0.729-0.1,1.093v0.291c0,7.759,8.53,14.025,19.033,14.025h34.762    c10.502,0,18.983-6.266,18.983-14.025v-0.145h7.593v0.145c0,7.759,8.53,14.025,19.032,14.025h113.553    c10.503,0,19.032-6.266,19.032-14.025v-0.145h513.627v0.145c0,7.759,8.53,14.025,19.032,14.025h113.553    c10.502,0,19.033-6.266,19.033-14.025v-0.145h7.592v0.145c0,7.759,8.481,14.025,18.982,14.025h34.762    c10.502,0,18.983-6.266,18.983-14.025v-0.291c0-0.437-0.049-0.911-0.099-1.348c35.452-3.023,68.782-10.782,98.367-22.548    c39.2-15.518,71.84-38.065,94.176-66.042l1.134-1.348c4.191-5.245,7.939-10.636,11.341-16.137    c7.544-12.349,12.968-25.208,16.272-38.394l0.001-0.011l27.12-0.005v-54.439h-25.249l-0.001-0.002l-10.55-67.025l-5.45-34.615    v-58.637l4.323-0.002c5.804-0.003,10.549-4.33,11.314-9.923l36.629-7.982c6.595-1.463,11.295-7.25,11.291-14.008v-0.012l0,0    l-0.012-23.248c0-0.974-0.109-1.948-0.326-2.867c-1.085-5.458-5.357-9.891-10.981-11.076l-36.639-7.941    c-0.771-5.592-5.52-9.913-11.323-9.909l-4.275,0.002V2221.323L2484.551,2221.321z"/>
		<g>
			<g>
				<rect x="1181.722" y="1754.205" style="fill:#000301;" width="1060.096" height="103.899"/>
				<g>
					<g>
						<g>
							<g>
								<g>
									<g>
										<path d="M2416.894,924.062c-33.101,218.398-243.485,143.972-243.693-25.295            c-0.139-140.628-384.739,320.417-461.394-112.614c-76.655,433.031-461.253-28.014-461.393,112.614            c-0.209,169.268-210.662,243.693-243.763,25.295c-29.896-197.142,86.411-267.804,139.931-350.174            c5.784-8.919,10.871-17.909,14.912-27.247c11.499-26.481,42.858-41.115,83.136-49.757            c105.576-22.718,272.963-4.6,307.527-51.637c8.641-11.777,19.512-20.836,31.498-27.735            c36.794-21.255,84.111-22.369,110.522-20.906c11.777,0.628,23.484,0.628,35.191,0            c26.482-1.463,73.799-0.349,110.524,20.906c11.986,6.899,22.857,15.959,31.498,27.735            c47.874,64.948,349.058,5.575,390.661,101.394C2303.654,642.46,2449.995,705.666,2416.894,924.062z"/>
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter" filterUnits="userSpaceOnUse" x="1001.814" y="396.169" width="1419.912" height="662.975">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										<mask maskUnits="userSpaceOnUse" x="1001.814" y="396.169" width="1419.912" height="662.975" id="SVGID_1_">
											<g style="filter:url(#Adobe_OpacityMaskFilter);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000136385322144237913240000004882233570463589566_" filterUnits="userSpaceOnUse" x="1001.814" y="396.169" width="1419.912" height="662.975">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												<mask maskUnits="userSpaceOnUse" x="1001.814" y="396.169" width="1419.912" height="662.975" id="SVGID_1_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000136385322144237913240000004882233570463589566_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000157286368220808735730000010746450397263219591_" gradientUnits="userSpaceOnUse" x1="1001.8135" y1="727.6563" x2="2421.7251" y2="727.6563">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.0074" style="stop-color:#EEEEEE"/>
													<stop offset="0.0273" style="stop-color:#C4C4C4"/>
													<stop offset="0.0477" style="stop-color:#A2A2A2"/>
													<stop offset="0.0683" style="stop-color:#888888"/>
													<stop offset="0.0894" style="stop-color:#757575"/>
													<stop offset="0.1111" style="stop-color:#6A6A6A"/>
													<stop offset="0.1344" style="stop-color:#666666"/>
													<stop offset="0.3495" style="stop-color:#666666"/>
													<stop offset="0.4821" style="stop-color:#5D5D5D"/>
													<stop offset="0.7004" style="stop-color:#444444"/>
													<stop offset="0.8226" style="stop-color:#333333"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="mask:url(#SVGID_1_);fill:url(#SVGID_00000157286368220808735730000010746450397263219591_);" d="              M2416.894,924.062c-33.101,218.398-243.485,143.972-243.693-25.295c-0.139-140.628-384.739,320.417-461.394-112.614              c-76.655,433.031-461.253-28.014-461.393,112.614c-0.209,169.268-210.662,243.693-243.763,25.295              c-29.896-197.142,86.411-267.804,139.931-350.174c5.784-8.919,10.871-17.909,14.912-27.247              c11.499-26.481,42.858-41.115,83.136-49.757c105.576-22.718,272.963-4.6,307.527-51.637              c8.641-11.777,19.512-20.836,31.498-27.735c36.794-21.255,84.111-22.369,110.522-20.906              c11.777,0.628,23.484,0.628,35.191,0c26.482-1.463,73.799-0.349,110.524,20.906              c11.986,6.899,22.857,15.959,31.498,27.735c47.874,64.948,349.058,5.575,390.661,101.394              C2303.654,642.46,2449.995,705.666,2416.894,924.062z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000081619628639145942290000000615545811309336466_" gradientUnits="userSpaceOnUse" x1="1001.8135" y1="727.6563" x2="2421.7251" y2="727.6563">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0027" style="stop-color:#F2F2F2"/>
											<stop offset="0.018" style="stop-color:#BABABA"/>
											<stop offset="0.0338" style="stop-color:#898989"/>
											<stop offset="0.0498" style="stop-color:#5F5F5F"/>
											<stop offset="0.0659" style="stop-color:#3C3C3C"/>
											<stop offset="0.0822" style="stop-color:#222222"/>
											<stop offset="0.0989" style="stop-color:#0F0F0F"/>
											<stop offset="0.116" style="stop-color:#040404"/>
											<stop offset="0.1344" style="stop-color:#000000"/>
											<stop offset="0.1553" style="stop-color:#090909"/>
											<stop offset="0.1899" style="stop-color:#222222"/>
											<stop offset="0.2339" style="stop-color:#4A4B4B"/>
											<stop offset="0.2855" style="stop-color:#828384"/>
											<stop offset="0.3429" style="stop-color:#C9CACB"/>
											<stop offset="0.3495" style="stop-color:#D1D3D4"/>
											<stop offset="0.4084" style="stop-color:#C8CACB"/>
											<stop offset="0.5058" style="stop-color:#B0B1B2"/>
											<stop offset="0.6294" style="stop-color:#888889"/>
											<stop offset="0.7733" style="stop-color:#525051"/>
											<stop offset="0.8226" style="stop-color:#3E3B3C"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<path style="opacity:0.4;fill:url(#SVGID_00000081619628639145942290000000615545811309336466_);" d="M2416.894,924.062            c-33.101,218.398-243.485,143.972-243.693-25.295c-0.139-140.628-384.739,320.417-461.394-112.614            c-76.655,433.031-461.253-28.014-461.393,112.614c-0.209,169.268-210.662,243.693-243.763,25.295            c-29.896-197.142,86.411-267.804,139.931-350.174c5.784-8.919,10.871-17.909,14.912-27.247            c11.499-26.481,42.858-41.115,83.136-49.757c105.576-22.718,272.963-4.6,307.527-51.637            c8.641-11.777,19.512-20.836,31.498-27.735c36.794-21.255,84.111-22.369,110.522-20.906            c11.777,0.628,23.484,0.628,35.191,0c26.482-1.463,73.799-0.349,110.524,20.906c11.986,6.899,22.857,15.959,31.498,27.735            c47.874,64.948,349.058,5.575,390.661,101.394C2303.654,642.46,2449.995,705.666,2416.894,924.062z"/>
									</g>
									<g>
										<g>
											<path style="fill:#000301;" d="M1864.845,405.323c-2.224,8.764-12.194,14.238-22.311,12.501             c-93.087-10.344-168.408-10.343-261.497-0.002c-10.113,1.737-20.08-3.734-22.304-12.499l0,0             c-2.225-8.765,4.579-17.588,15.241-19.418c90.839-15.581,184.796-15.579,275.634-0.002             C1860.27,387.735,1867.069,396.558,1864.845,405.323L1864.845,405.323z"/>
										</g>
										<g style="opacity:0.4;">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000175289316819447136150000011466198955735536795_" filterUnits="userSpaceOnUse" x="1558.312" y="374.22" width="306.955" height="43.925">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1558.312" y="374.22" width="306.955" height="43.925" id="SVGID_00000150801514127480379760000005687135093297007021_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000175289316819447136150000011466198955735536795_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000045618431427550451280000002196261655742393233_" filterUnits="userSpaceOnUse" x="1558.312" y="374.22" width="306.955" height="43.925">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1558.312" y="374.22" width="306.955" height="43.925" id="SVGID_00000150801514127480379760000005687135093297007021_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000045618431427550451280000002196261655742393233_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000120539413902127324940000010447644230794749098_" gradientUnits="userSpaceOnUse" x1="1558.3115" y1="396.1826" x2="1865.2665" y2="396.1826">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0074" style="stop-color:#EEEEEE"/>
														<stop offset="0.0273" style="stop-color:#C4C4C4"/>
														<stop offset="0.0477" style="stop-color:#A2A2A2"/>
														<stop offset="0.0683" style="stop-color:#888888"/>
														<stop offset="0.0894" style="stop-color:#757575"/>
														<stop offset="0.1111" style="stop-color:#6A6A6A"/>
														<stop offset="0.1344" style="stop-color:#666666"/>
														<stop offset="0.3495" style="stop-color:#666666"/>
														<stop offset="0.4821" style="stop-color:#5D5D5D"/>
														<stop offset="0.7004" style="stop-color:#444444"/>
														<stop offset="0.8226" style="stop-color:#333333"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000150801514127480379760000005687135093297007021_);fill:url(#SVGID_00000120539413902127324940000010447644230794749098_);" d="               M1864.845,405.323c-2.224,8.764-12.194,14.238-22.311,12.501c-93.087-10.344-168.408-10.343-261.497-0.002               c-10.113,1.737-20.08-3.734-22.304-12.499l0,0c-2.225-8.765,4.579-17.588,15.241-19.418               c90.839-15.581,184.796-15.579,275.634-0.002C1860.27,387.735,1867.069,396.558,1864.845,405.323L1864.845,405.323z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000108296813364668940900000018427879770835487652_" gradientUnits="userSpaceOnUse" x1="1558.3115" y1="396.1826" x2="1865.2665" y2="396.1826">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0027" style="stop-color:#F2F2F2"/>
												<stop offset="0.018" style="stop-color:#BABABA"/>
												<stop offset="0.0338" style="stop-color:#898989"/>
												<stop offset="0.0498" style="stop-color:#5F5F5F"/>
												<stop offset="0.0659" style="stop-color:#3C3C3C"/>
												<stop offset="0.0822" style="stop-color:#222222"/>
												<stop offset="0.0989" style="stop-color:#0F0F0F"/>
												<stop offset="0.116" style="stop-color:#040404"/>
												<stop offset="0.1344" style="stop-color:#000000"/>
												<stop offset="0.1553" style="stop-color:#090909"/>
												<stop offset="0.1899" style="stop-color:#222222"/>
												<stop offset="0.2339" style="stop-color:#4A4B4B"/>
												<stop offset="0.2855" style="stop-color:#828384"/>
												<stop offset="0.3429" style="stop-color:#C9CACB"/>
												<stop offset="0.3495" style="stop-color:#D1D3D4"/>
												<stop offset="0.4084" style="stop-color:#C8CACB"/>
												<stop offset="0.5058" style="stop-color:#B0B1B2"/>
												<stop offset="0.6294" style="stop-color:#888889"/>
												<stop offset="0.7733" style="stop-color:#525051"/>
												<stop offset="0.8226" style="stop-color:#3E3B3C"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000108296813364668940900000018427879770835487652_);" d="M1864.845,405.323             c-2.224,8.764-12.194,14.238-22.311,12.501c-93.087-10.344-168.408-10.343-261.497-0.002             c-10.113,1.737-20.08-3.734-22.304-12.499l0,0c-2.225-8.765,4.579-17.588,15.241-19.418             c90.839-15.581,184.796-15.579,275.634-0.002C1860.27,387.735,1867.069,396.558,1864.845,405.323L1864.845,405.323z"/>
										</g>
									</g>
								</g>
								<g>
									<path style="fill:#E27B04;" d="M1244.629,496.885l-1.393,9.548c-6.69,45.087-43.903,76.934-83.066,71.08           c-4.738-0.696-9.269-1.882-13.588-3.624c5.784-8.919,10.871-17.909,14.912-27.247           C1172.993,520.16,1204.352,505.526,1244.629,496.885z"/>
									<path style="fill:#E27B04;" d="M2178.46,496.123l1.393,9.547c6.69,45.087,43.903,76.934,83.065,71.08           c4.739-0.695,9.269-1.882,13.589-3.624c-5.784-8.919-10.872-17.908-14.912-27.247           C2250.096,519.398,2218.738,504.763,2178.46,496.123z"/>
								</g>
							</g>
							<g style="opacity:0.54;">
								<g>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000102533908708420925680000005053181675086899848_" filterUnits="userSpaceOnUse" x="1146.582" y="496.885" width="98.048" height="81.321">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1146.582" y="496.885" width="98.048" height="81.321" id="SVGID_00000094607400568540831400000004366425844788463017_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000102533908708420925680000005053181675086899848_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000096765528256149516920000015462863317321927072_" filterUnits="userSpaceOnUse" x="1146.582" y="496.885" width="98.048" height="81.321">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1146.582" y="496.885" width="98.048" height="81.321" id="SVGID_00000094607400568540831400000004366425844788463017_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000096765528256149516920000015462863317321927072_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000080885496442000970050000017685671850856660876_" gradientUnits="userSpaceOnUse" x1="1146.5817" y1="537.5452" x2="1244.6294" y2="537.5452">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0575" style="stop-color:#F0F0F0"/>
												<stop offset="0.1676" style="stop-color:#CACACA"/>
												<stop offset="0.3174" style="stop-color:#8C8C8C"/>
												<stop offset="0.457" style="stop-color:#4D4D4D"/>
												<stop offset="0.517" style="stop-color:#565656"/>
												<stop offset="0.6162" style="stop-color:#6F6F6F"/>
												<stop offset="0.7423" style="stop-color:#989898"/>
												<stop offset="0.8887" style="stop-color:#D0D0D0"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000094607400568540831400000004366425844788463017_);fill:url(#SVGID_00000080885496442000970050000017685671850856660876_);" d="             M1244.629,496.885l-1.393,9.548c-6.69,45.087-43.903,76.934-83.066,71.08c-4.738-0.696-9.269-1.882-13.588-3.624             c5.784-8.919,10.871-17.909,14.912-27.247C1172.993,520.16,1204.352,505.526,1244.629,496.885z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000103231536357485192530000012872108527767547556_" gradientUnits="userSpaceOnUse" x1="1146.5817" y1="537.5452" x2="1244.6294" y2="537.5452">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0475" style="stop-color:#EEF4F9"/>
										<stop offset="0.1384" style="stop-color:#C8DDEE"/>
										<stop offset="0.2634" style="stop-color:#89B7DC"/>
										<stop offset="0.4163" style="stop-color:#3483C3"/>
										<stop offset="0.457" style="stop-color:#1C75BC"/>
										<stop offset="0.5072" style="stop-color:#257BBF"/>
										<stop offset="0.5902" style="stop-color:#3E8AC6"/>
										<stop offset="0.6957" style="stop-color:#67A3D2"/>
										<stop offset="0.8195" style="stop-color:#A0C5E3"/>
										<stop offset="0.9573" style="stop-color:#E7F1F8"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000103231536357485192530000012872108527767547556_);" d="M1244.629,496.885l-1.393,9.548           c-6.69,45.087-43.903,76.934-83.066,71.08c-4.738-0.696-9.269-1.882-13.588-3.624c5.784-8.919,10.871-17.909,14.912-27.247           C1172.993,520.16,1204.352,505.526,1244.629,496.885z"/>
								</g>
								<g>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000003820229130621837450000014421646264843676298_" filterUnits="userSpaceOnUse" x="2178.46" y="496.123" width="98.048" height="81.32">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2178.46" y="496.123" width="98.048" height="81.32" id="SVGID_00000035504434281769454240000016112402205240847542_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000003820229130621837450000014421646264843676298_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000129184278170930730860000013304285396528087954_" filterUnits="userSpaceOnUse" x="2178.46" y="496.123" width="98.048" height="81.32">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2178.46" y="496.123" width="98.048" height="81.32" id="SVGID_00000035504434281769454240000016112402205240847542_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000129184278170930730860000013304285396528087954_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000119799983359194038750000004887282356987486391_" gradientUnits="userSpaceOnUse" x1="2178.4602" y1="536.7831" x2="2276.5078" y2="536.7831">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0575" style="stop-color:#F0F0F0"/>
												<stop offset="0.1676" style="stop-color:#CACACA"/>
												<stop offset="0.3174" style="stop-color:#8C8C8C"/>
												<stop offset="0.457" style="stop-color:#4D4D4D"/>
												<stop offset="0.517" style="stop-color:#565656"/>
												<stop offset="0.6162" style="stop-color:#6F6F6F"/>
												<stop offset="0.7423" style="stop-color:#989898"/>
												<stop offset="0.8887" style="stop-color:#D0D0D0"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000035504434281769454240000016112402205240847542_);fill:url(#SVGID_00000119799983359194038750000004887282356987486391_);" d="             M2178.46,496.123l1.393,9.547c6.69,45.087,43.903,76.934,83.065,71.08c4.739-0.695,9.269-1.882,13.589-3.624             c-5.784-8.919-10.872-17.908-14.912-27.247C2250.096,519.398,2218.738,504.763,2178.46,496.123z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000091732961143115175890000017041947060755387301_" gradientUnits="userSpaceOnUse" x1="2178.4602" y1="536.7831" x2="2276.5078" y2="536.7831">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0475" style="stop-color:#EEF4F9"/>
										<stop offset="0.1384" style="stop-color:#C8DDEE"/>
										<stop offset="0.2634" style="stop-color:#89B7DC"/>
										<stop offset="0.4163" style="stop-color:#3483C3"/>
										<stop offset="0.457" style="stop-color:#1C75BC"/>
										<stop offset="0.5072" style="stop-color:#257BBF"/>
										<stop offset="0.5902" style="stop-color:#3E8AC6"/>
										<stop offset="0.6957" style="stop-color:#67A3D2"/>
										<stop offset="0.8195" style="stop-color:#A0C5E3"/>
										<stop offset="0.9573" style="stop-color:#E7F1F8"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000091732961143115175890000017041947060755387301_);" d="M2178.46,496.123l1.393,9.547           c6.69,45.087,43.903,76.934,83.065,71.08c4.739-0.695,9.269-1.882,13.589-3.624c-5.784-8.919-10.872-17.908-14.912-27.247           C2250.096,519.398,2218.738,504.763,2178.46,496.123z"/>
								</g>
							</g>
						</g>
						<g>
							<g>
								<polygon style="fill:#73788E;" points="2309.286,1189.228 2309.286,1354.086 2366.003,1354.086 2327.975,1243.57                   "/>
								<path style="fill:#A2B2B1;" d="M2309.274,1354.086v373.361h0.783c42.08,0,75.492-35.402,73.061-77.411l-17.126-295.949          H2309.274z"/>
								<g style="opacity:0.3;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000013895319444718788740000011292479032062938534_" filterUnits="userSpaceOnUse" x="2309.286" y="1189.228" width="56.717" height="164.858">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2309.286" y="1189.228" width="56.717" height="164.858" id="SVGID_00000176737521042472391380000016236753973288479666_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000013895319444718788740000011292479032062938534_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000128468219828973466440000004485318186683756192_" filterUnits="userSpaceOnUse" x="2309.286" y="1189.228" width="56.717" height="164.858">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2309.286" y="1189.228" width="56.717" height="164.858" id="SVGID_00000176737521042472391380000016236753973288479666_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000128468219828973466440000004485318186683756192_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000052084316415364138560000007585307064442844845_" gradientUnits="userSpaceOnUse" x1="-4874.5898" y1="1271.6573" x2="-4817.873" y2="1271.6573" gradientTransform="matrix(-1 0 0 1 -2508.5869 0)">
												<stop offset="0.2634" style="stop-color:#FFFFFF"/>
												<stop offset="0.3222" style="stop-color:#F6F6F6"/>
												<stop offset="0.4197" style="stop-color:#DCDCDC"/>
												<stop offset="0.5439" style="stop-color:#B3B3B3"/>
												<stop offset="0.6886" style="stop-color:#7A7A7A"/>
												<stop offset="0.7366" style="stop-color:#666666"/>
												<stop offset="0.7823" style="stop-color:#6A6A6A"/>
												<stop offset="0.8248" style="stop-color:#757575"/>
												<stop offset="0.8661" style="stop-color:#888888"/>
												<stop offset="0.9066" style="stop-color:#A2A2A2"/>
												<stop offset="0.9465" style="stop-color:#C4C4C4"/>
												<stop offset="0.9855" style="stop-color:#EEEEEE"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<polygon style="mask:url(#SVGID_00000176737521042472391380000016236753973288479666_);fill:url(#SVGID_00000052084316415364138560000007585307064442844845_);" points="             2309.286,1189.228 2309.286,1354.086 2366.003,1354.086 2327.975,1243.57            "/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000098901288475264997610000001589767672939317655_" gradientUnits="userSpaceOnUse" x1="-4874.5898" y1="1271.6573" x2="-4817.873" y2="1271.6573" gradientTransform="matrix(-1 0 0 1 -2508.5869 0)">
										<stop offset="0.2634" style="stop-color:#FFFFFF"/>
										<stop offset="0.3036" style="stop-color:#F6F6F6"/>
										<stop offset="0.3703" style="stop-color:#DCDCDC"/>
										<stop offset="0.4553" style="stop-color:#B3B3B3"/>
										<stop offset="0.5553" style="stop-color:#797979"/>
										<stop offset="0.6665" style="stop-color:#313131"/>
										<stop offset="0.7366" style="stop-color:#000000"/>
										<stop offset="0.7726" style="stop-color:#040404"/>
										<stop offset="0.8062" style="stop-color:#0F0F0F"/>
										<stop offset="0.8388" style="stop-color:#222222"/>
										<stop offset="0.8708" style="stop-color:#3C3C3C"/>
										<stop offset="0.9024" style="stop-color:#5F5F5F"/>
										<stop offset="0.9337" style="stop-color:#898989"/>
										<stop offset="0.9647" style="stop-color:#BABABA"/>
										<stop offset="0.9948" style="stop-color:#F2F2F2"/>
										<stop offset="1" style="stop-color:#FDFDFD"/>
									</linearGradient>
									<polygon style="fill:url(#SVGID_00000098901288475264997610000001589767672939317655_);" points="2309.286,1189.228            2309.286,1354.086 2366.003,1354.086 2327.975,1243.57          "/>
								</g>
								<g style="opacity:0.3;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000014597800901584018290000003431619127772369047_" filterUnits="userSpaceOnUse" x="2309.274" y="1354.086" width="73.968" height="373.361">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2309.274" y="1354.086" width="73.968" height="373.361" id="SVGID_00000002345343650448563280000003814213861933862831_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000014597800901584018290000003431619127772369047_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000174571213451970472100000016239663717323891597_" filterUnits="userSpaceOnUse" x="2309.274" y="1354.086" width="73.968" height="373.361">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2309.274" y="1354.086" width="73.968" height="373.361" id="SVGID_00000002345343650448563280000003814213861933862831_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000174571213451970472100000016239663717323891597_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000100347545189373393790000012078827283507977129_" gradientUnits="userSpaceOnUse" x1="-4891.8296" y1="1540.7666" x2="-4817.8613" y2="1540.7666" gradientTransform="matrix(-1 0 0 1 -2508.5869 0)">
												<stop offset="0.2634" style="stop-color:#FFFFFF"/>
												<stop offset="0.3222" style="stop-color:#F6F6F6"/>
												<stop offset="0.4197" style="stop-color:#DCDCDC"/>
												<stop offset="0.5439" style="stop-color:#B3B3B3"/>
												<stop offset="0.6886" style="stop-color:#7A7A7A"/>
												<stop offset="0.7366" style="stop-color:#666666"/>
												<stop offset="0.7823" style="stop-color:#6A6A6A"/>
												<stop offset="0.8248" style="stop-color:#757575"/>
												<stop offset="0.8661" style="stop-color:#888888"/>
												<stop offset="0.9066" style="stop-color:#A2A2A2"/>
												<stop offset="0.9465" style="stop-color:#C4C4C4"/>
												<stop offset="0.9855" style="stop-color:#EEEEEE"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000002345343650448563280000003814213861933862831_);fill:url(#SVGID_00000100347545189373393790000012078827283507977129_);" d="             M2309.274,1354.086v373.361h0.783c42.08,0,75.492-35.402,73.061-77.411l-17.126-295.949H2309.274z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000085956387506195361030000001434362962237546929_" gradientUnits="userSpaceOnUse" x1="-4891.8296" y1="1540.7666" x2="-4817.8613" y2="1540.7666" gradientTransform="matrix(-1 0 0 1 -2508.5869 0)">
										<stop offset="0.2634" style="stop-color:#FFFFFF"/>
										<stop offset="0.3036" style="stop-color:#F6F6F6"/>
										<stop offset="0.3703" style="stop-color:#DCDCDC"/>
										<stop offset="0.4553" style="stop-color:#B3B3B3"/>
										<stop offset="0.5553" style="stop-color:#797979"/>
										<stop offset="0.6665" style="stop-color:#313131"/>
										<stop offset="0.7366" style="stop-color:#000000"/>
										<stop offset="0.7726" style="stop-color:#040404"/>
										<stop offset="0.8062" style="stop-color:#0F0F0F"/>
										<stop offset="0.8388" style="stop-color:#222222"/>
										<stop offset="0.8708" style="stop-color:#3C3C3C"/>
										<stop offset="0.9024" style="stop-color:#5F5F5F"/>
										<stop offset="0.9337" style="stop-color:#898989"/>
										<stop offset="0.9647" style="stop-color:#BABABA"/>
										<stop offset="0.9948" style="stop-color:#F2F2F2"/>
										<stop offset="1" style="stop-color:#FDFDFD"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000085956387506195361030000001434362962237546929_);" d="M2309.274,1354.086v373.361h0.783           c42.08,0,75.492-35.402,73.061-77.411l-17.126-295.949H2309.274z"/>
								</g>
							</g>
							<g>
								<polygon style="fill:#73788E;" points="1114.254,1189.228 1114.254,1354.086 1057.536,1354.086 1095.564,1243.57                   "/>
								<path style="fill:#A2B2B1;" d="M1114.265,1354.086v373.361h-0.783c-42.08,0-75.491-35.402-73.061-77.411l17.127-295.949          H1114.265z"/>
								<g style="opacity:0.3;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000000194497480507490430000001777498242701037709_" filterUnits="userSpaceOnUse" x="1057.536" y="1189.228" width="56.717" height="164.858">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1057.536" y="1189.228" width="56.717" height="164.858" id="SVGID_00000000204985936318156500000009732476887959798666_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000000194497480507490430000001777498242701037709_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000061450811164994646610000010154413369575142033_" filterUnits="userSpaceOnUse" x="1057.536" y="1189.228" width="56.717" height="164.858">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1057.536" y="1189.228" width="56.717" height="164.858" id="SVGID_00000000204985936318156500000009732476887959798666_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000061450811164994646610000010154413369575142033_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000041257226102029847740000013009555114270133432_" gradientUnits="userSpaceOnUse" x1="1057.5365" y1="1271.6573" x2="1114.2535" y2="1271.6573">
												<stop offset="0.2634" style="stop-color:#FFFFFF"/>
												<stop offset="0.3222" style="stop-color:#F6F6F6"/>
												<stop offset="0.4197" style="stop-color:#DCDCDC"/>
												<stop offset="0.5439" style="stop-color:#B3B3B3"/>
												<stop offset="0.6886" style="stop-color:#7A7A7A"/>
												<stop offset="0.7366" style="stop-color:#666666"/>
												<stop offset="0.7823" style="stop-color:#6A6A6A"/>
												<stop offset="0.8248" style="stop-color:#757575"/>
												<stop offset="0.8661" style="stop-color:#888888"/>
												<stop offset="0.9066" style="stop-color:#A2A2A2"/>
												<stop offset="0.9465" style="stop-color:#C4C4C4"/>
												<stop offset="0.9855" style="stop-color:#EEEEEE"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<polygon style="mask:url(#SVGID_00000000204985936318156500000009732476887959798666_);fill:url(#SVGID_00000041257226102029847740000013009555114270133432_);" points="             1114.254,1189.228 1114.254,1354.086 1057.536,1354.086 1095.564,1243.57            "/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000054980901153637658190000010754799931163643031_" gradientUnits="userSpaceOnUse" x1="1057.5365" y1="1271.6573" x2="1114.2535" y2="1271.6573">
										<stop offset="0.2634" style="stop-color:#FFFFFF"/>
										<stop offset="0.3036" style="stop-color:#F6F6F6"/>
										<stop offset="0.3703" style="stop-color:#DCDCDC"/>
										<stop offset="0.4553" style="stop-color:#B3B3B3"/>
										<stop offset="0.5553" style="stop-color:#797979"/>
										<stop offset="0.6665" style="stop-color:#313131"/>
										<stop offset="0.7366" style="stop-color:#000000"/>
										<stop offset="0.7726" style="stop-color:#040404"/>
										<stop offset="0.8062" style="stop-color:#0F0F0F"/>
										<stop offset="0.8388" style="stop-color:#222222"/>
										<stop offset="0.8708" style="stop-color:#3C3C3C"/>
										<stop offset="0.9024" style="stop-color:#5F5F5F"/>
										<stop offset="0.9337" style="stop-color:#898989"/>
										<stop offset="0.9647" style="stop-color:#BABABA"/>
										<stop offset="0.9948" style="stop-color:#F2F2F2"/>
										<stop offset="1" style="stop-color:#FDFDFD"/>
									</linearGradient>
									<polygon style="fill:url(#SVGID_00000054980901153637658190000010754799931163643031_);" points="1114.254,1189.228            1114.254,1354.086 1057.536,1354.086 1095.564,1243.57          "/>
								</g>
								<g style="opacity:0.3;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000023975030762715451160000005925149782784327585_" filterUnits="userSpaceOnUse" x="1040.296" y="1354.086" width="73.968" height="373.361">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1040.296" y="1354.086" width="73.968" height="373.361" id="SVGID_00000038401534564039719970000005022073410733003932_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000023975030762715451160000005925149782784327585_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000072270480896896298210000008915594883223245497_" filterUnits="userSpaceOnUse" x="1040.296" y="1354.086" width="73.968" height="373.361">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1040.296" y="1354.086" width="73.968" height="373.361" id="SVGID_00000038401534564039719970000005022073410733003932_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000072270480896896298210000008915594883223245497_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000052794480478623317790000007652486178255463870_" gradientUnits="userSpaceOnUse" x1="1040.2963" y1="1540.7666" x2="1114.2645" y2="1540.7666">
												<stop offset="0.2634" style="stop-color:#FFFFFF"/>
												<stop offset="0.3222" style="stop-color:#F6F6F6"/>
												<stop offset="0.4197" style="stop-color:#DCDCDC"/>
												<stop offset="0.5439" style="stop-color:#B3B3B3"/>
												<stop offset="0.6886" style="stop-color:#7A7A7A"/>
												<stop offset="0.7366" style="stop-color:#666666"/>
												<stop offset="0.7823" style="stop-color:#6A6A6A"/>
												<stop offset="0.8248" style="stop-color:#757575"/>
												<stop offset="0.8661" style="stop-color:#888888"/>
												<stop offset="0.9066" style="stop-color:#A2A2A2"/>
												<stop offset="0.9465" style="stop-color:#C4C4C4"/>
												<stop offset="0.9855" style="stop-color:#EEEEEE"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000038401534564039719970000005022073410733003932_);fill:url(#SVGID_00000052794480478623317790000007652486178255463870_);" d="             M1114.265,1354.086v373.361h-0.783c-42.08,0-75.491-35.402-73.061-77.411l17.127-295.949H1114.265z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000089534733214014565830000017563102526804086441_" gradientUnits="userSpaceOnUse" x1="1040.2963" y1="1540.7666" x2="1114.2645" y2="1540.7666">
										<stop offset="0.2634" style="stop-color:#FFFFFF"/>
										<stop offset="0.3036" style="stop-color:#F6F6F6"/>
										<stop offset="0.3703" style="stop-color:#DCDCDC"/>
										<stop offset="0.4553" style="stop-color:#B3B3B3"/>
										<stop offset="0.5553" style="stop-color:#797979"/>
										<stop offset="0.6665" style="stop-color:#313131"/>
										<stop offset="0.7366" style="stop-color:#000000"/>
										<stop offset="0.7726" style="stop-color:#040404"/>
										<stop offset="0.8062" style="stop-color:#0F0F0F"/>
										<stop offset="0.8388" style="stop-color:#222222"/>
										<stop offset="0.8708" style="stop-color:#3C3C3C"/>
										<stop offset="0.9024" style="stop-color:#5F5F5F"/>
										<stop offset="0.9337" style="stop-color:#898989"/>
										<stop offset="0.9647" style="stop-color:#BABABA"/>
										<stop offset="0.9948" style="stop-color:#F2F2F2"/>
										<stop offset="1" style="stop-color:#FDFDFD"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000089534733214014565830000017563102526804086441_);" d="M1114.265,1354.086v373.361           h-0.783c-42.08,0-75.491-35.402-73.061-77.411l17.127-295.949H1114.265z"/>
								</g>
							</g>
							<g>
								<g>
									<g>
										<path style="fill:#771D26;" d="M2356.492,915.618v298.188c0,2.299-0.069,4.529-0.209,6.76            c-1.045,17.073-5.645,33.727-13.519,48.988l-0.278,0.488c-6.064,11.708-9.687,24.599-10.523,37.701            c-0.139,1.882-0.279,3.693-0.279,5.575l-6.69,356.028c0,1.393-0.069,2.858-0.209,4.251            c-2.718,49.756-32.962,94.147-78.676,114.704l-1.045,0.488c-13.868,6.202-28.642,10.035-43.693,11.219            c-3.693,0.418-7.457,0.558-11.22,0.558h-956.515c-3.762,0-7.525-0.14-11.219-0.558            c-15.053-1.184-29.826-5.017-43.694-11.219l-1.046-0.488c-25.575-11.499-46.271-30.454-60.069-53.589            c-10.872-18.258-17.422-39.164-18.608-61.115c-0.14-1.393-0.208-2.857-0.208-4.251l-5.784-308.502l-0.905-47.526            c0-1.882-0.14-3.693-0.279-5.575c-0.837-13.101-4.461-25.993-10.522-37.701l-0.279-0.488            c-7.944-15.262-12.474-31.916-13.521-48.988c-0.137-2.231-0.208-4.461-0.208-6.76V915.618            c0-8.641,0.279-17.283,0.766-25.924c1.535-25.993,5.575-51.847,12.753-76.933            c98.188-341.951,631.081-332.753,631.081-332.753s532.821-9.199,631.009,332.753c7.178,25.085,11.289,50.94,12.822,76.933            C2356.214,898.335,2356.492,906.977,2356.492,915.618z"/>
										<g>
											<path style="fill:#F8AF0A;" d="M1093.965,1051.912v11.637c0,16.447-11.358,30.245-26.689,33.869v-79.373             c6.341,1.533,11.986,4.739,16.445,9.198C1090.062,1033.585,1093.965,1042.295,1093.965,1051.912z"/>
											<path style="fill:#F8AF0A;" d="M2329.785,1051.912v11.637c0,16.447,11.358,30.245,26.688,33.869v-79.373             c-6.34,1.533-11.986,4.739-16.445,9.198C2333.688,1033.585,2329.785,1042.295,2329.785,1051.912z"/>
										</g>
										<g style="opacity:0.54;">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000052091962439003049620000014425117179657542541_" filterUnits="userSpaceOnUse" x="1067.276" y="1018.045" width="26.689" height="79.373">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1067.276" y="1018.045" width="26.689" height="79.373" id="SVGID_00000163073547511480175870000010224817016692766133_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000052091962439003049620000014425117179657542541_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000113336131233354073930000004431566119348532109_" filterUnits="userSpaceOnUse" x="1067.276" y="1018.045" width="26.689" height="79.373">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1067.276" y="1018.045" width="26.689" height="79.373" id="SVGID_00000163073547511480175870000010224817016692766133_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000113336131233354073930000004431566119348532109_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000058560104187622386560000013702492167770922941_" gradientUnits="userSpaceOnUse" x1="1067.276" y1="1057.7314" x2="1093.9646" y2="1057.7314">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000163073547511480175870000010224817016692766133_);fill:url(#SVGID_00000058560104187622386560000013702492167770922941_);" d="               M1093.965,1051.912v11.637c0,16.447-11.358,30.245-26.689,33.869v-79.373c6.341,1.533,11.986,4.739,16.445,9.198               C1090.062,1033.585,1093.965,1042.295,1093.965,1051.912z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000104702647559367012130000002409090114720668861_" gradientUnits="userSpaceOnUse" x1="1067.276" y1="1057.7314" x2="1093.9646" y2="1057.7314">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000104702647559367012130000002409090114720668861_);" d="M1093.965,1051.912v11.637             c0,16.447-11.358,30.245-26.689,33.869v-79.373c6.341,1.533,11.986,4.739,16.445,9.198             C1090.062,1033.585,1093.965,1042.295,1093.965,1051.912z"/>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000064317681238618464800000015887034440572716206_" filterUnits="userSpaceOnUse" x="2329.785" y="1018.045" width="26.689" height="79.373">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2329.785" y="1018.045" width="26.689" height="79.373" id="SVGID_00000171706509345655356070000003928901288420286364_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000064317681238618464800000015887034440572716206_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000023274286643421960340000004610325338704221619_" filterUnits="userSpaceOnUse" x="2329.785" y="1018.045" width="26.689" height="79.373">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2329.785" y="1018.045" width="26.689" height="79.373" id="SVGID_00000171706509345655356070000003928901288420286364_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000023274286643421960340000004610325338704221619_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000083773812069747882270000011112032701029802387_" gradientUnits="userSpaceOnUse" x1="2329.7852" y1="1057.7314" x2="2356.4736" y2="1057.7314">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000171706509345655356070000003928901288420286364_);fill:url(#SVGID_00000083773812069747882270000011112032701029802387_);" d="               M2329.785,1051.912v11.637c0,16.447,11.358,30.245,26.688,33.869v-79.373c-6.34,1.533-11.986,4.739-16.445,9.198               C2333.688,1033.585,2329.785,1042.295,2329.785,1051.912z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000063603295033687083900000011366699358564212404_" gradientUnits="userSpaceOnUse" x1="2329.7852" y1="1057.7314" x2="2356.4736" y2="1057.7314">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000063603295033687083900000011366699358564212404_);" d="M2329.785,1051.912v11.637             c0,16.447,11.358,30.245,26.688,33.869v-79.373c-6.34,1.533-11.986,4.739-16.445,9.198             C2333.688,1033.585,2329.785,1042.295,2329.785,1051.912z"/>
										</g>
									</g>
									<g style="opacity:0.14;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000096772450484699710740000012582934688287046847_" filterUnits="userSpaceOnUse" x="1067.276" y="479.972" width="1289.198" height="523.369">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1067.276" y="479.972" width="1289.198" height="523.369" id="SVGID_00000168090976489920050540000012852697572307449984_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000096772450484699710740000012582934688287046847_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000021093420736544926520000003959007750186329993_" filterUnits="userSpaceOnUse" x="1067.276" y="479.972" width="1289.198" height="523.369">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1067.276" y="479.972" width="1289.198" height="523.369" id="SVGID_00000168090976489920050540000012852697572307449984_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000021093420736544926520000003959007750186329993_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000167365660474888291270000014966934524358261635_" gradientUnits="userSpaceOnUse" x1="1711.8749" y1="1003.3406" x2="1711.8749" y2="479.9717">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000168090976489920050540000012852697572307449984_);fill:url(#SVGID_00000167365660474888291270000014966934524358261635_);" d="              M2356.474,915.048v88.293c0-34.565-4.042-69.129-13.588-102.3c-98.189-341.951-631.01-332.752-631.01-332.752              s-532.892-9.198-631.081,332.752c-9.477,33.171-13.519,67.735-13.519,102.3v-88.293c0-34.564,4.042-69.129,13.519-102.3              c98.189-341.951,631.081-332.752,631.081-332.752s532.821-9.198,631.01,332.752              C2352.431,845.919,2356.474,880.484,2356.474,915.048z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000142135073830525272700000017355608226103382716_" gradientUnits="userSpaceOnUse" x1="1711.8749" y1="1003.3406" x2="1711.8749" y2="479.9717">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000142135073830525272700000017355608226103382716_);" d="M2356.474,915.048v88.293            c0-34.565-4.042-69.129-13.588-102.3c-98.189-341.951-631.01-332.752-631.01-332.752s-532.892-9.198-631.081,332.752            c-9.477,33.171-13.519,67.735-13.519,102.3v-88.293c0-34.564,4.042-69.129,13.519-102.3            c98.189-341.951,631.081-332.752,631.081-332.752s532.821-9.198,631.01,332.752            C2352.431,845.919,2356.474,880.484,2356.474,915.048z"/>
									</g>
									<g style="opacity:0.14;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000009574126433234297530000003578656461407523247_" filterUnits="userSpaceOnUse" x="1067.295" y="1124.956" width="1289.198" height="675.61">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1067.295" y="1124.956" width="1289.198" height="675.61" id="SVGID_00000121252346481712082110000013593993033412202629_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000009574126433234297530000003578656461407523247_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000075861904680427064270000016030600588190417827_" filterUnits="userSpaceOnUse" x="1067.295" y="1124.956" width="1289.198" height="675.61">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1067.295" y="1124.956" width="1289.198" height="675.61" id="SVGID_00000121252346481712082110000013593993033412202629_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000075861904680427064270000016030600588190417827_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000111156273584690264260000006095506215669618582_" gradientUnits="userSpaceOnUse" x1="1711.8936" y1="1124.9557" x2="1711.8936" y2="1800.5654">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000121252346481712082110000013593993033412202629_);fill:url(#SVGID_00000111156273584690264260000006095506215669618582_);" d="              M2356.492,1124.956v88.85c0,2.299-0.069,4.529-0.209,6.76c-1.045,17.073-5.645,33.727-13.519,48.988l-0.278,0.488              c-6.064,11.708-9.687,24.599-10.523,37.701c-0.139,1.882-0.279,3.693-0.279,5.575l-6.69,356.028              c0,1.393-0.069,2.858-0.209,4.251c-2.718,49.756-32.962,94.147-78.676,114.704l-1.045,0.488              c-13.868,6.202-28.642,10.035-43.693,11.219c-3.693,0.418-7.457,0.558-11.22,0.558h-956.515              c-3.762,0-7.525-0.14-11.219-0.558c-15.053-1.184-29.826-5.017-43.694-11.219l-1.046-0.488              c-25.575-11.499-46.271-30.454-60.069-53.589c-10.872-18.258-17.422-39.164-18.608-61.115              c-0.14-1.393-0.208-2.857-0.208-4.251l-5.784-308.502l-0.905-47.526c0-1.882-0.14-3.693-0.279-5.575              c-0.837-13.101-4.461-25.993-10.522-37.701l-0.279-0.488c-7.944-15.262-12.474-31.916-13.521-48.988              c-0.137-2.231-0.208-4.461-0.208-6.76v-88.85c0,19.581,4.668,38.884,13.728,56.306l0.279,0.489              c6.829,13.17,10.522,27.805,10.802,42.648l2.508,134.076l4.181,222.021c0.905,49.059,28.641,93.45,71.776,116.028              c2.3,1.254,4.67,2.369,7.108,3.484l1.046,0.487c13.31,5.924,27.386,9.687,41.812,11.081              c4.319,0.487,8.711,0.697,13.1,0.697h956.515c4.391,0,8.711-0.21,13.102-0.697c14.355-1.394,28.5-5.157,41.812-11.081              l1.045-0.487c47.177-21.254,77.909-67.805,78.885-119.512l6.69-356.097c0.279-14.843,3.972-29.478,10.802-42.648              l0.278-0.489C2351.754,1163.84,2356.492,1144.537,2356.492,1124.956z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000049904477295538434830000007695375084148160404_" gradientUnits="userSpaceOnUse" x1="1711.8936" y1="1124.9557" x2="1711.8936" y2="1800.5654">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000049904477295538434830000007695375084148160404_);" d="M2356.492,1124.956v88.85            c0,2.299-0.069,4.529-0.209,6.76c-1.045,17.073-5.645,33.727-13.519,48.988l-0.278,0.488            c-6.064,11.708-9.687,24.599-10.523,37.701c-0.139,1.882-0.279,3.693-0.279,5.575l-6.69,356.028            c0,1.393-0.069,2.858-0.209,4.251c-2.718,49.756-32.962,94.147-78.676,114.704l-1.045,0.488            c-13.868,6.202-28.642,10.035-43.693,11.219c-3.693,0.418-7.457,0.558-11.22,0.558h-956.515            c-3.762,0-7.525-0.14-11.219-0.558c-15.053-1.184-29.826-5.017-43.694-11.219l-1.046-0.488            c-25.575-11.499-46.271-30.454-60.069-53.589c-10.872-18.258-17.422-39.164-18.608-61.115            c-0.14-1.393-0.208-2.857-0.208-4.251l-5.784-308.502l-0.905-47.526c0-1.882-0.14-3.693-0.279-5.575            c-0.837-13.101-4.461-25.993-10.522-37.701l-0.279-0.488c-7.944-15.262-12.474-31.916-13.521-48.988            c-0.137-2.231-0.208-4.461-0.208-6.76v-88.85c0,19.581,4.668,38.884,13.728,56.306l0.279,0.489            c6.829,13.17,10.522,27.805,10.802,42.648l2.508,134.076l4.181,222.021c0.905,49.059,28.641,93.45,71.776,116.028            c2.3,1.254,4.67,2.369,7.108,3.484l1.046,0.487c13.31,5.924,27.386,9.687,41.812,11.081            c4.319,0.487,8.711,0.697,13.1,0.697h956.515c4.391,0,8.711-0.21,13.102-0.697c14.355-1.394,28.5-5.157,41.812-11.081            l1.045-0.487c47.177-21.254,77.909-67.805,78.885-119.512l6.69-356.097c0.279-14.843,3.972-29.478,10.802-42.648            l0.278-0.489C2351.754,1163.84,2356.492,1144.537,2356.492,1124.956z"/>
									</g>
									<g style="opacity:0.12;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000020389844776235767500000010392684007404827538_" filterUnits="userSpaceOnUse" x="1067.295" y="559.869" width="251.776" height="1240.696">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1067.295" y="559.869" width="251.776" height="1240.696" id="SVGID_00000138544322987785163640000003392809569786850732_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000020389844776235767500000010392684007404827538_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000075850543319170642300000013169144957669880996_" filterUnits="userSpaceOnUse" x="1067.295" y="559.869" width="251.776" height="1240.696">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1067.295" y="559.869" width="251.776" height="1240.696" id="SVGID_00000138544322987785163640000003392809569786850732_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000075850543319170642300000013169144957669880996_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000138547584943292467620000014825118430792715963_" gradientUnits="userSpaceOnUse" x1="1319.0708" y1="1180.2172" x2="1067.2947" y2="1180.2172">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000138544322987785163640000003392809569786850732_);fill:url(#SVGID_00000138547584943292467620000014825118430792715963_);" d="              M1207.226,1076.732c-0.28,25.505-0.072,52.125,0.557,79.791c0.835,35.819,1.672,70.104,2.439,102.996v0.21              c0.138,6.063,0.278,12.125,0.417,18.118c0.349,14.982,0.698,29.617,1.047,43.972v0.07              c0.14,6.062,0.279,12.055,0.417,18.048c3.695,159.304,6.412,281.046,8.434,371.638              c0.695,33.658,1.324,63.065,1.882,88.432v0.558c-15.053-1.185-29.826-4.948-43.694-11.22l-1.046-0.418              c-26.133-11.778-47.178-31.291-60.976-55.192c-10.383-18.049-16.654-38.607-17.701-60.14              c-0.14-1.393-0.208-2.857-0.208-4.251l-5.784-308.502l-0.905-47.526c0-1.882-0.14-3.693-0.279-5.575              c-0.977-12.962-4.53-25.575-10.593-37.142l-0.209-0.418c-8.015-15.401-12.614-32.264-13.521-49.616              c-0.137-2.231-0.208-4.461-0.208-6.76V915.618c0-8.641,0.279-17.283,0.766-25.924              c1.603-25.785,5.645-51.428,12.753-76.375c23.553-82.091,72.194-143.903,132.474-190.522              c32.404-25.088,68.224-45.715,105.365-62.718c0.14-0.07,0.279-0.14,0.418-0.209              c-18.257,28.571-37.351,66.341-54.426,115.957c-20.906,60.628-38.884,138.955-48.988,239.722              c-0.628,6.341-1.187,12.821-1.814,19.372v0.07c-1.81,20.628-3.274,42.159-4.39,64.599              C1208.27,1024.119,1207.503,1049.834,1207.226,1076.732z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000016053783612432956510000000348323537885338249_" gradientUnits="userSpaceOnUse" x1="1319.0708" y1="1180.2172" x2="1067.2947" y2="1180.2172">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000016053783612432956510000000348323537885338249_);" d="M1207.226,1076.732            c-0.28,25.505-0.072,52.125,0.557,79.791c0.835,35.819,1.672,70.104,2.439,102.996v0.21            c0.138,6.063,0.278,12.125,0.417,18.118c0.349,14.982,0.698,29.617,1.047,43.972v0.07            c0.14,6.062,0.279,12.055,0.417,18.048c3.695,159.304,6.412,281.046,8.434,371.638            c0.695,33.658,1.324,63.065,1.882,88.432v0.558c-15.053-1.185-29.826-4.948-43.694-11.22l-1.046-0.418            c-26.133-11.778-47.178-31.291-60.976-55.192c-10.383-18.049-16.654-38.607-17.701-60.14            c-0.14-1.393-0.208-2.857-0.208-4.251l-5.784-308.502l-0.905-47.526c0-1.882-0.14-3.693-0.279-5.575            c-0.977-12.962-4.53-25.575-10.593-37.142l-0.209-0.418c-8.015-15.401-12.614-32.264-13.521-49.616            c-0.137-2.231-0.208-4.461-0.208-6.76V915.618c0-8.641,0.279-17.283,0.766-25.924            c1.603-25.785,5.645-51.428,12.753-76.375c23.553-82.091,72.194-143.903,132.474-190.522            c32.404-25.088,68.224-45.715,105.365-62.718c0.14-0.07,0.279-0.14,0.418-0.209            c-18.257,28.571-37.351,66.341-54.426,115.957c-20.906,60.628-38.884,138.955-48.988,239.722            c-0.628,6.341-1.187,12.821-1.814,19.372v0.07c-1.81,20.628-3.274,42.159-4.39,64.599            C1208.27,1024.119,1207.503,1049.834,1207.226,1076.732z"/>
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000138547026765770283680000010313818668127382686_" filterUnits="userSpaceOnUse" x="1206.787" y="512.58" width="250.086" height="760.901">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1206.787" y="512.58" width="250.086" height="760.901" id="SVGID_00000057830052463649602240000001833531512061543863_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000138547026765770283680000010313818668127382686_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000011724973036899758940000002885302054054349500_" filterUnits="userSpaceOnUse" x="1206.787" y="512.58" width="250.086" height="760.901">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1206.787" y="512.58" width="250.086" height="760.901" id="SVGID_00000057830052463649602240000001833531512061543863_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000011724973036899758940000002885302054054349500_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000078727930035961958750000008759026202908149903_" gradientUnits="userSpaceOnUse" x1="1456.873" y1="893.0303" x2="1206.787" y2="893.0303">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000057830052463649602240000001833531512061543863_);fill:url(#SVGID_00000078727930035961958750000008759026202908149903_);" d="              M1350.322,1024.217c17.491,254.076-64.391,270.662-140.419,235.4c-0.766-32.891-1.602-67.178-2.438-102.997              c-7.944-335.47,55.401-509.268,111.289-596.655c45.365-20.766,92.682-36.098,138.118-47.386              C1398.546,598.015,1331.925,756.622,1350.322,1024.217z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000076582151419828605810000004372759366203941015_" gradientUnits="userSpaceOnUse" x1="1456.873" y1="893.0303" x2="1206.787" y2="893.0303">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000076582151419828605810000004372759366203941015_);" d="M1350.322,1024.217            c17.491,254.076-64.391,270.662-140.419,235.4c-0.766-32.891-1.602-67.178-2.438-102.997            c-7.944-335.47,55.401-509.268,111.289-596.655c45.365-20.766,92.682-36.098,138.118-47.386            C1398.546,598.015,1331.925,756.622,1350.322,1024.217z"/>
									</g>
									<g style="opacity:0.12;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000008839127620853391140000008951362102176558493_" filterUnits="userSpaceOnUse" x="2104.696" y="559.857" width="251.778" height="1240.696">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2104.696" y="559.857" width="251.778" height="1240.696" id="SVGID_00000044159707663579706040000002520398760525841559_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000008839127620853391140000008951362102176558493_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000081615907357600739790000006376619775246700721_" filterUnits="userSpaceOnUse" x="2104.696" y="559.857" width="251.778" height="1240.696">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2104.696" y="559.857" width="251.778" height="1240.696" id="SVGID_00000044159707663579706040000002520398760525841559_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000081615907357600739790000006376619775246700721_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000010278474955859673030000009909647187424880802_" gradientUnits="userSpaceOnUse" x1="-4807.9233" y1="1180.2051" x2="-5059.7007" y2="1180.2051" gradientTransform="matrix(-1 0 0 1 -2703.2271 0)">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000044159707663579706040000002520398760525841559_);fill:url(#SVGID_00000010278474955859673030000009909647187424880802_);" d="              M2215.986,1156.512c7.945-335.471-55.4-509.269-111.29-596.655c37.283,17.073,73.241,37.77,105.784,62.927              c60.279,46.62,108.92,108.431,132.474,190.522c9.477,33.24,13.52,67.735,13.52,102.299v298.189              c0,19.651-4.669,38.955-13.729,56.377l-0.208,0.418c-6.899,13.24-10.593,27.805-10.871,42.718l-6.69,356.028              c-0.977,51.776-31.707,98.326-78.885,119.581l-1.045,0.419c-13.868,6.271-28.642,10.034-43.693,11.218              c2.229-104.878,5.992-277.282,12.195-541.045C2214.313,1226.617,2215.15,1192.33,2215.986,1156.512z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000126321540656865567480000005086936913322104237_" gradientUnits="userSpaceOnUse" x1="-4807.9233" y1="1180.2051" x2="-5059.7007" y2="1180.2051" gradientTransform="matrix(-1 0 0 1 -2703.2271 0)">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000126321540656865567480000005086936913322104237_);" d="M2215.986,1156.512            c7.945-335.471-55.4-509.269-111.29-596.655c37.283,17.073,73.241,37.77,105.784,62.927            c60.279,46.62,108.92,108.431,132.474,190.522c9.477,33.24,13.52,67.735,13.52,102.299v298.189            c0,19.651-4.669,38.955-13.729,56.377l-0.208,0.418c-6.899,13.24-10.593,27.805-10.871,42.718l-6.69,356.028            c-0.977,51.776-31.707,98.326-78.885,119.581l-1.045,0.419c-13.868,6.271-28.642,10.034-43.693,11.218            c2.229-104.878,5.992-277.282,12.195-541.045C2214.313,1226.617,2215.15,1192.33,2215.986,1156.512z"/>
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000019667322296640015930000008969734279416555693_" filterUnits="userSpaceOnUse" x="1966.877" y="512.58" width="250.086" height="760.901">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1966.877" y="512.58" width="250.086" height="760.901" id="SVGID_00000104692500208066947440000016085485522217998476_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000019667322296640015930000008969734279416555693_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000168810106865874387960000013523347171229874840_" filterUnits="userSpaceOnUse" x="1966.877" y="512.58" width="250.086" height="760.901">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1966.877" y="512.58" width="250.086" height="760.901" id="SVGID_00000104692500208066947440000016085485522217998476_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000168810106865874387960000013523347171229874840_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000088096753788142629770000013893645568881466513_" gradientUnits="userSpaceOnUse" x1="-4670.1045" y1="893.0303" x2="-4920.1904" y2="893.0303" gradientTransform="matrix(-1 0 0 1 -2703.2271 0)">
													<stop offset="0.043" style="stop-color:#4D4D4D"/>
													<stop offset="0.2157" style="stop-color:#6F6F6F"/>
													<stop offset="0.595" style="stop-color:#C6C6C6"/>
													<stop offset="0.8333" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000104692500208066947440000016085485522217998476_);fill:url(#SVGID_00000088096753788142629770000013893645568881466513_);" d="              M2073.428,1024.217c-17.492,254.076,64.39,270.662,140.418,235.4c0.767-32.891,1.603-67.178,2.439-102.997              c7.944-335.47-55.401-509.268-111.29-596.655c-45.365-20.766-92.682-36.098-138.118-47.386              C2025.205,598.015,2091.825,756.622,2073.428,1024.217z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000121995318543007151080000008083446046031973054_" gradientUnits="userSpaceOnUse" x1="-4670.1045" y1="893.0303" x2="-4920.1904" y2="893.0303" gradientTransform="matrix(-1 0 0 1 -2703.2271 0)">
											<stop offset="0.043" style="stop-color:#FFFFFF"/>
											<stop offset="0.1832" style="stop-color:#DDDDDD"/>
											<stop offset="0.4946" style="stop-color:#878585"/>
											<stop offset="0.8333" style="stop-color:#231F20"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000121995318543007151080000008083446046031973054_);" d="M2073.428,1024.217            c-17.492,254.076,64.39,270.662,140.418,235.4c0.767-32.891,1.603-67.178,2.439-102.997            c7.944-335.47-55.401-509.268-111.29-596.655c-45.365-20.766-92.682-36.098-138.118-47.386            C2025.205,598.015,2091.825,756.622,2073.428,1024.217z"/>
									</g>
								</g>
								<g>
									<path style="fill:#262626;" d="M2282.33,973.236l-10.612,101.989l-12.347,118.825l-12.193,117.244           c-0.714,6.276-5.561,11.02-11.377,11.02l-65.255-1.378h-0.153l-25.409-0.561l-24.132-0.51l-9.183-0.204l-24.234-0.51           l-9.132-0.204l-24.337-0.51l-9.184-0.153l-24.234-0.561l-9.234-0.153l-24.439-0.561l-57.601-1.174l-68.367-1.479           l-57.499-1.224l-24.336-0.511l-9.133-0.204l-24.285-0.51l-9.132-0.204l-24.337-0.51h-0.561l-8.571,0.153l-24.286,0.51           l-9.184,0.204l-24.388,0.51l-57.449,1.225l-25.714,0.51l-42.959,0.969l-57.754,1.225l-23.979,0.51l-9.185,0.204           l-24.234,0.511l-9.184,0.153l-24.285,0.51l-9.184,0.204l-24.235,0.561l-9.234,0.205l-23.673,0.51l-57.448,1.174           l-58.469,1.275c-6.02,0-10.919-4.745-11.531-11.02l-22.908-220.1l-12.296-117.957c-0.459-4.541,1.326-8.98,4.694-11.479           c0.612-0.511,1.275-0.919,1.938-1.225c7.602-3.622,15.459-7.041,23.571-10.306c19.898-8.112,41.275-15.409,63.571-21.888           c1.48-0.408,2.908-0.816,4.439-1.225c86.938-24.744,188.161-38.316,274.589-45.662c1.378-0.153,2.704-0.255,4.031-0.408           h0.051c96.121-8.01,173.11-8.52,190.1-8.52h6.837c30.714,0,255.609,1.582,436.832,46.173c0.051,0,0.051,0,0.051,0           c2.245,0.51,4.439,1.071,6.633,1.633c42.601,10.765,82.601,23.979,116.835,40.203           C2280.238,962.726,2282.891,967.878,2282.33,973.236z"/>
									<g>
										<path style="fill:#05040C;" d="M2282.322,973.261l-10.617,101.941l-3.364,0.125l-419.368,19.786            c0,0-49.195,17.838-157.619,8.919c-105.117-8.605-133.022-77.005-134.646-81.277c-0.058-0.188-0.058-0.25-0.058-0.25            c-21,8.479-47.338,13.881-75.821,17.147c-3.83,0.439-7.716,0.816-11.603,1.193c-1.393,0.126-2.726,0.251-4.12,0.314            c-8.875,0.754-17.925,1.382-27.033,1.759c-16.416,0.753-33.009,0.941-49.252,0.817c-0.058,0-0.058,0-0.116,0            c-2.726,0-5.453,0-8.121-0.063h-0.059c-18.331-0.251-36.024-1.005-52.443-1.947h-0.058            c-9.399-0.503-18.332-1.131-26.686-1.759h-0.347c-5.687-0.439-11.081-0.879-16.186-1.318            c-29.413-2.45-48.44-4.963-48.44-4.963c-2.552,24.559-18.622,40.199-18.622,40.199l-61.377,16.645l-2.61,0.691            l-12.298-117.958c-0.464-4.585,1.334-8.982,4.698-11.494c0.639-0.503,1.278-0.942,1.972-1.256            c7.6-3.58,15.431-7.035,23.553-10.301c19.898-8.102,41.246-15.388,63.581-21.857c1.45-0.44,2.9-0.817,4.409-1.257            c86.96-24.747,188.19-38.314,274.628-45.662c1.335-0.126,2.669-0.251,4.004-0.377h0.058            c96.125-8.039,173.106-8.542,190.106-8.542h6.845c30.689,0,255.601,1.57,436.831,46.165c0.058,0,0.058,0,0.058,0            c2.203,0.503,4.409,1.068,6.613,1.633c42.581,10.803,82.609,23.994,116.837,40.199            C2280.234,962.709,2282.903,967.859,2282.322,973.261z"/>
										<g>
											<path d="M1239.673,927.095c-0.464,1.319-0.986,2.575-1.509,3.768c-6.033,14.698-9.397,24.057-9.572,24.496             c-0.405,1.131-1.45,1.696-2.262,1.068c-0.812-0.503-1.219-1.821-0.812-2.952c0.522-1.507,3.306-9.296,8.005-20.916             c0.523-1.381,1.102-2.764,1.741-4.208C1236.714,927.912,1238.164,927.536,1239.673,927.095z"/>
											<path d="M2158.814,920.312c-0.753,0.377-1.45,0.691-2.203,1.068c-21.292,10.176-46.99,20.79-77.331,32.222             c-68.28,25.752-111.905,34.985-141.955,34.985c-37.359,0-53.893-14.195-70.948-28.892             c-23.67-20.413-48.092-41.518-134.066-23.868c-24.191,4.898-44.379,9.547-62.247,13.566             c-101.405,22.989-105.639,23.931-155.762-67.96c1.335-0.126,2.669-0.251,4.004-0.377h0.058             c47.569,87.431,52.676,86.301,151.12,63.94c17.869-4.082,38.056-8.605,62.249-13.63             c87.308-17.838,112.31,3.706,136.502,24.559c30.108,26.003,58.591,50.499,210.119-6.595             c27.786-10.489,51.746-20.413,72.05-29.835c0.58-0.251,1.161-0.565,1.741-0.817c0.058,0,0.058,0,0.058,0             C2154.405,919.182,2156.611,919.747,2158.814,920.312z"/>
										</g>
										<path style="fill:#151618;" d="M1205.503,978.788l-14.908,58.539c-2.264,8.73-8.007,15.011-14.967,16.268            c-9.108,1.57-20.479,3.58-21.174,3.58c-0.929,0-6.672-73.928-8.296-95.409c0.639-0.503,1.278-0.942,1.972-1.256            c7.6-3.58,15.431-7.035,23.553-10.301l13.4-2.951C1198.716,944.305,1209.914,961.515,1205.503,978.788z"/>
										<path style="fill:#151618;" d="M2264.942,955.935c0,0-7.625,101.209-8.754,101.209c-0.673,0-12.058-1.979-21.161-3.579            c-6.927-1.219-12.722-7.52-14.945-16.249l-14.909-58.536c-4.398-17.268,6.748-34.488,20.384-31.494L2264.942,955.935z"/>
									</g>
									<g>
										<path style="fill:#05040C;" d="M1531.88,1181.727l-1.393,14.196l-4.988,52.132l-6.44,67.207l-57.722,1.256l-24.016,0.503            l-9.167,0.188l-24.249,0.503l-9.167,0.188l-24.307,0.503l-9.167,0.189l-24.248,0.565l-9.225,0.188l-23.669,0.503            l-57.431,1.194l-6.439-67.081l-6.963-72.232c-0.927-9.295-0.289-18.403,1.741-27.008            c8.527-37.12,41.943-65.323,82.434-65.323h130.181C1497.594,1089.396,1536.694,1132.233,1531.88,1181.727z"/>
										<path style="fill:#0F2315;" d="M1476.478,1159.932l-3.888,40.073l-3.711,38.564l-7.542,77.947l-24.016,0.503l-9.167,0.188            l-24.249,0.503l-9.167,0.188l-24.307,0.503l-9.167,0.189l-24.248,0.565l-9.225,0.188l-23.669,0.503l-7.541-78.01            l-6.15-63.815l-1.74-18.089c-0.579-5.778,1.915-9.798,4.06-12.185c2.147-2.387,5.976-5.214,11.777-5.214h156.11            c5.802,0,9.631,2.827,11.778,5.214C1474.563,1150.134,1476.999,1154.154,1476.478,1159.932z"/>
										<g style="opacity:0.17;">
											<polygon style="fill:#5F8969;" points="1337.017,1089.396 1337.017,1319.155 1327.792,1319.343 1327.792,1089.396                         "/>
											<polygon style="fill:#5F8969;" points="1370.432,1089.396 1370.432,1318.401 1361.265,1318.59 1361.265,1089.396                         "/>
											<polygon style="fill:#5F8969;" points="1403.905,1089.396 1403.905,1317.711 1394.738,1317.899 1394.738,1089.396                         "/>
											<polygon style="fill:#5F8969;" points="1437.32,1089.396 1437.32,1317.02 1428.154,1317.208 1428.154,1089.396                         "/>
										</g>
										<path style="opacity:0.06;fill:#FFFFFF;" d="M1531.88,1181.735l-1.381,14.166c-18.552,2.221-37.905,3.597-57.928,4.13            c-6.02,0.177-12.13,0.266-18.284,0.266c-5.708,0-11.372-0.089-16.945-0.222c-3.077-0.045-6.154-0.178-9.186-0.266            c-8.206-0.312-16.322-0.8-24.26-1.421c-3.077-0.222-6.154-0.444-9.185-0.755c-8.25-0.711-16.323-1.598-24.26-2.665            c-3.077-0.4-6.154-0.844-9.186-1.288c-8.295-1.199-16.366-2.576-24.26-4.13c-3.121-0.577-6.154-1.199-9.185-1.865            c-13.157-2.798-25.642-6.04-37.37-9.681c-21.316-6.572-40.045-14.432-55.43-23.269            c8.562-37.124,41.963-65.323,82.454-65.323h130.169C1497.588,1089.413,1536.696,1132.222,1531.88,1181.735z"/>
										<path style="opacity:0.26;fill:#090C09;" d="M1525.498,1248.054l-6.44,67.207l-57.722,1.256l-24.016,0.503l-9.167,0.188            l-24.249,0.503l-9.167,0.188l-24.307,0.503l-9.167,0.189l-24.248,0.565l-9.225,0.188l-23.669,0.503l-57.431,1.194            l-6.439-67.081c17.344-4.9,36.257-8.982,56.329-12.123c10.152-1.633,20.536-2.952,31.209-4.02            c3.075-0.377,6.15-0.628,9.225-0.879c7.947-0.691,16.069-1.256,24.248-1.633c3.017-0.188,6.092-0.314,9.167-0.44            c8.005-0.314,16.127-0.44,24.307-0.44c3.075,0,6.15,0,9.167,0.127c8.179,0.063,16.242,0.314,24.249,0.753            c3.075,0.125,6.15,0.314,9.167,0.503c10.789,0.691,21.29,1.633,31.558,2.763            C1488.776,1240.894,1507.804,1244.035,1525.498,1248.054z"/>
									</g>
									<g>
										<path style="fill:#05040C;" d="M1873.661,1181.753l-1.428,14.132v0.052l-4.949,52.142l-6.378,66.275l-57.499-1.224            l-24.336-0.511l-9.133-0.204l-24.285-0.51l-9.132-0.204l-24.337-0.51h-0.561l-8.571,0.153l-24.286,0.51l-9.184,0.204            l-24.388,0.51l-57.449,1.225l-5.713-59.846l-6.99-72.244c-0.918-9.286-0.255-18.368,1.735-26.989            c8.571-37.091,41.989-65.306,82.448-65.306h130.202C1839.375,1089.407,1878.457,1132.213,1873.661,1181.753z"/>
										<path style="fill:#0F2315;" d="M1818.254,1159.917l-3.878,40.101l-3.725,38.571l-7.245,74.54l-24.336-0.511l-9.133-0.204            l-24.285-0.51l-9.132-0.204l-24.337-0.51h-0.561l-8.571,0.153l-24.286,0.51l-9.184,0.204l-24.388,0.51l-6.837-70.714            l-6.173-63.826l-1.734-18.111c-0.561-5.766,1.938-9.796,4.081-12.194c2.143-2.346,5.969-5.204,11.785-5.204h156.07            c5.817,0,9.643,2.858,11.785,5.204C1816.315,1150.121,1818.763,1154.151,1818.254,1159.917z"/>
										<g style="opacity:0.17;">
											<polygon style="fill:#5F8969;" points="1678.765,1089.407 1678.765,1311.854 1669.582,1312.058 1669.582,1089.407                         "/>
											<polygon style="fill:#5F8969;" points="1712.183,1089.407 1712.183,1311.191 1711.621,1311.191 1703.051,1311.344              1703.051,1089.407            "/>
											<polygon style="fill:#5F8969;" points="1745.652,1089.407 1745.652,1311.905 1736.52,1311.701 1736.52,1089.407                         "/>
											<polygon style="fill:#5F8969;" points="1779.07,1089.407 1779.07,1312.619 1769.937,1312.415 1769.937,1089.407                         "/>
										</g>
										<path style="opacity:0.06;fill:#FFFFFF;" d="M1873.649,1181.735l-1.381,14.166c-18.552,2.221-37.905,3.597-57.928,4.13            c-6.02,0.177-12.13,0.266-18.284,0.266c-5.708,0-11.372-0.089-16.945-0.222c-3.077-0.045-6.154-0.178-9.186-0.266            c-8.206-0.312-16.322-0.8-24.26-1.421c-3.077-0.222-6.153-0.444-9.185-0.755c-8.25-0.711-16.322-1.598-24.26-2.665            c-3.077-0.4-6.154-0.844-9.186-1.288c-8.295-1.199-16.366-2.576-24.26-4.13c-3.121-0.577-6.154-1.199-9.185-1.865            c-13.156-2.798-25.641-6.04-37.37-9.681c-21.316-6.572-40.045-14.432-55.43-23.269            c8.562-37.124,41.963-65.323,82.454-65.323h130.169C1839.357,1089.413,1878.465,1132.222,1873.649,1181.735z"/>
										<path style="opacity:0.26;fill:#090C09;" d="M1867.283,1248.079l-6.378,66.275l-57.499-1.224l-24.336-0.511l-9.133-0.204            l-24.285-0.51l-9.132-0.204l-24.337-0.51h-0.561l-8.571,0.153l-24.286,0.51l-9.184,0.204l-24.388,0.51l-57.449,1.225            l-5.713-59.846c17.346-4.898,36.224-8.98,56.325-12.092c10.153-1.633,20.51-2.959,31.224-4.031            c3.061-0.408,6.122-0.612,9.184-0.867c7.959-0.714,16.071-1.276,24.286-1.633c3.01-0.204,6.071-0.357,9.132-0.459            c8.011-0.306,16.123-0.459,24.337-0.459c3.061,0,6.122,0,9.132,0.153c8.214,0.051,16.276,0.306,24.285,0.765            c3.061,0.102,6.122,0.307,9.133,0.459c10.816,0.715,21.326,1.633,31.581,2.807            C1830.549,1240.885,1849.579,1244.049,1867.283,1248.079z"/>
									</g>
									<g>
										<path style="fill:#05040C;" d="M2215.442,1183.794l-1.428,14.438v0.052l-4.999,53.265l-3.818,70.118l-34.652-0.732h-0.153            l-25.409-0.561l-24.132-0.51l-9.183-0.204l-24.234-0.51l-9.132-0.204l-24.337-0.51l-9.184-0.153l-24.234-0.561            l-9.234-0.153l-24.439-0.561l-57.601-1.174l-5.459-58.264l-6.99-73.827c-0.918-9.49-0.306-18.724,1.734-27.55            c1.582-7.143,4.082-13.929,7.296-20.255c0.816-1.633,1.683-3.214,2.653-4.745c0.051-0.153,0.153-0.306,0.255-0.459            c0.969-1.582,1.938-3.164,3.01-4.694c1.072-1.581,2.245-3.112,3.419-4.592c1.173-1.48,2.398-2.959,3.725-4.337            c2.551-2.857,5.306-5.51,8.213-7.959c1.428-1.225,2.96-2.398,4.49-3.571c2.296-1.684,4.693-3.266,7.193-4.694            c0.816-0.51,1.633-0.969,2.5-1.428c0.817-0.46,1.684-0.919,2.551-1.327c0.612-0.306,1.174-0.612,1.786-0.867            c0.714-0.357,1.378-0.663,2.092-0.969c4.898-2.092,10-3.776,15.306-4.949c2.245-0.51,4.49-0.867,6.785-1.173            c1.225-0.204,2.449-0.306,3.674-0.408c1.225-0.153,2.448-0.204,3.725-0.255c1.276-0.051,2.499-0.102,3.776-0.102h130.151            c8.827,0,17.347,1.378,25.306,3.929c2.653,0.816,5.255,1.837,7.806,2.907c1.275,0.561,2.551,1.123,3.775,1.735            c2.448,1.225,4.847,2.551,7.194,3.979c2.296,1.48,4.54,3.01,6.734,4.694c6.735,5.153,12.705,11.326,17.653,18.214            c0.663,0.867,1.275,1.786,1.836,2.704c0.817,1.173,1.582,2.398,2.296,3.622c0.664,1.072,1.275,2.193,1.837,3.317            c0.459,0.816,0.867,1.633,1.275,2.449c0.561,1.174,1.122,2.347,1.633,3.52c0.459,0.969,0.867,1.99,1.275,3.01            c0.408,1.021,0.765,2.041,1.123,3.061c0.357,0.918,0.663,1.837,0.918,2.755c0.153,0.408,0.255,0.816,0.408,1.224            c0.254,0.765,0.459,1.581,0.663,2.346c0.204,0.868,0.408,1.684,0.613,2.5c0.306,1.123,0.561,2.296,0.765,3.469            C2215.8,1166.855,2216.259,1175.223,2215.442,1183.794z"/>
										<path style="fill:#0F2315;" d="M2160.035,1161.499l-3.928,40.918v0.052l-3.673,39.387l-7.449,78.52l-24.132-0.51            l-9.183-0.204l-24.234-0.51l-9.132-0.204l-24.337-0.51l-9.184-0.153l-24.234-0.561l-9.234-0.153l-24.439-0.561            l-6.734-71.836l-6.173-65.204l-1.735-18.469c-0.561-5.918,1.939-10.051,4.081-12.449c2.143-2.449,5.97-5.357,11.735-5.357            h156.121c5.816,0,9.643,2.908,11.785,5.357C2158.096,1151.448,2160.545,1155.581,2160.035,1161.499z"/>
										<g style="opacity:0.17;">
											<polygon style="fill:#5F8969;" points="2020.547,1089.407 2020.547,1317.722 2011.313,1317.568 2011.313,1089.407                         "/>
											<polygon style="fill:#5F8969;" points="2053.965,1089.407 2053.965,1318.436 2044.781,1318.283 2044.781,1089.407                         "/>
											<polygon style="fill:#5F8969;" points="2087.434,1089.407 2087.434,1319.15 2078.302,1318.945 2078.302,1089.407                         "/>
											<polygon style="fill:#5F8969;" points="2120.852,1089.407 2120.852,1319.864 2111.668,1319.66 2111.668,1089.407                         "/>
										</g>
										<path style="opacity:0.06;fill:#FFFFFF;" d="M2215.418,1183.769l-1.382,14.477c-18.552,2.27-37.905,3.676-57.928,4.221            c-6.02,0.181-12.13,0.272-18.283,0.272c-5.709,0-11.372-0.091-16.946-0.227c-3.077-0.045-6.154-0.182-9.185-0.272            c-8.206-0.318-16.322-0.817-24.26-1.452c-3.077-0.227-6.153-0.454-9.186-0.772c-8.25-0.726-16.322-1.634-24.26-2.723            c-3.077-0.408-6.154-0.863-9.186-1.316c-8.294-1.225-16.366-2.633-24.259-4.221c-3.122-0.589-6.153-1.226-9.185-1.906            c-13.156-2.859-25.641-6.172-37.37-9.894c-21.316-6.717-40.045-14.75-55.429-23.781            c8.562-37.943,41.962-66.762,82.454-66.762h130.169C2181.125,1089.413,2220.235,1133.165,2215.418,1183.769z"/>
										<path style="opacity:0.26;fill:#090C09;" d="M2209.015,1251.549l-6.429,68.673l-32.04,0.714h-0.153l-25.409-0.561            l-24.132-0.51l-9.183-0.204l-24.234-0.51l-9.132-0.204l-24.337-0.51l-9.184-0.153l-24.234-0.561l-9.234-0.153            l-24.439-0.561l-57.601-1.174l-5.459-58.264c17.347-5,36.224-9.184,56.326-12.398c10.153-1.633,20.51-3.011,31.174-4.081            c3.112-0.408,6.173-0.663,9.234-0.919c7.959-0.714,16.071-1.275,24.234-1.633c3.061-0.204,6.123-0.357,9.184-0.459            c8.01-0.357,16.123-0.459,24.337-0.459c3.061,0,6.122,0,9.132,0.102c8.163,0.102,16.275,0.357,24.234,0.816            c3.112,0.102,6.173,0.307,9.183,0.51c10.816,0.663,21.276,1.633,31.582,2.806            C2172.331,1244.253,2191.362,1247.416,2209.015,1251.549z"/>
									</g>
									<g>
										<path style="fill:#050A10;" d="M1431.069,1024.161v-11.585l-48.472-11.673l-48.472,11.673v11.585            c0,9.863-4.924,18.95-12.862,23.734l-30.267,18.239l1.812,13.784c0,0,51.104-39.774,53.278-21.266            c1.398,11.899,16.088,13.707,26.401,13.481c6.741-0.148,13.48-0.148,20.22,0c10.315,0.226,25.004-1.582,26.402-13.481            c2.175-18.509,53.278,21.266,53.278,21.266l1.812-13.784l-30.267-18.239            C1435.994,1043.111,1431.069,1034.024,1431.069,1024.161z"/>
										<g style="opacity:0.33;">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000171713715958752455670000003321507837522837146_" filterUnits="userSpaceOnUse" x="1290.996" y="1000.904" width="183.203" height="79.014">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1290.996" y="1000.904" width="183.203" height="79.014" id="SVGID_00000072981718410085787980000000976292916964109952_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000171713715958752455670000003321507837522837146_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000135648953475805494920000018092645685992767423_" filterUnits="userSpaceOnUse" x="1290.996" y="1000.904" width="183.203" height="79.014">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1290.996" y="1000.904" width="183.203" height="79.014" id="SVGID_00000072981718410085787980000000976292916964109952_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000135648953475805494920000018092645685992767423_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000040574169666712835950000013983146195757740944_" gradientUnits="userSpaceOnUse" x1="1290.9958" y1="1040.4108" x2="1474.1993" y2="1040.4108">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.028" style="stop-color:#EEEEEE"/>
														<stop offset="0.1037" style="stop-color:#C4C4C4"/>
														<stop offset="0.1811" style="stop-color:#A2A2A2"/>
														<stop offset="0.2597" style="stop-color:#888888"/>
														<stop offset="0.3397" style="stop-color:#757575"/>
														<stop offset="0.4222" style="stop-color:#6A6A6A"/>
														<stop offset="0.5108" style="stop-color:#666666"/>
														<stop offset="0.5714" style="stop-color:#6F6F6F"/>
														<stop offset="0.6716" style="stop-color:#888888"/>
														<stop offset="0.7989" style="stop-color:#B1B1B1"/>
														<stop offset="0.9468" style="stop-color:#E9E9E9"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000072981718410085787980000000976292916964109952_);fill:url(#SVGID_00000040574169666712835950000013983146195757740944_);" d="               M1431.069,1024.161v-11.585l-48.472-11.673l-48.472,11.673v11.585c0,9.863-4.924,18.95-12.862,23.734l-30.267,18.239               l1.812,13.784c0,0,51.104-39.774,53.278-21.266c1.398,11.899,16.088,13.707,26.401,13.481               c6.741-0.148,13.48-0.148,20.22,0c10.315,0.226,25.004-1.582,26.402-13.481c2.175-18.509,53.278,21.266,53.278,21.266               l1.812-13.784l-30.267-18.239C1435.994,1043.111,1431.069,1034.024,1431.069,1024.161z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000091004691367752752950000012192595029549177245_" gradientUnits="userSpaceOnUse" x1="1290.9958" y1="1040.4108" x2="1474.1993" y2="1040.4108">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0102" style="stop-color:#F2F2F2"/>
												<stop offset="0.0685" style="stop-color:#BABABA"/>
												<stop offset="0.1286" style="stop-color:#898989"/>
												<stop offset="0.1892" style="stop-color:#5F5F5F"/>
												<stop offset="0.2504" style="stop-color:#3C3C3C"/>
												<stop offset="0.3125" style="stop-color:#222222"/>
												<stop offset="0.3757" style="stop-color:#0F0F0F"/>
												<stop offset="0.4408" style="stop-color:#040404"/>
												<stop offset="0.5108" style="stop-color:#000000"/>
												<stop offset="0.5523" style="stop-color:#090909"/>
												<stop offset="0.6209" style="stop-color:#222222"/>
												<stop offset="0.7081" style="stop-color:#4B4B4B"/>
												<stop offset="0.8105" style="stop-color:#848484"/>
												<stop offset="0.9244" style="stop-color:#CBCBCB"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000091004691367752752950000012192595029549177245_);" d="M1431.069,1024.161v-11.585             l-48.472-11.673l-48.472,11.673v11.585c0,9.863-4.924,18.95-12.862,23.734l-30.267,18.239l1.812,13.784             c0,0,51.104-39.774,53.278-21.266c1.398,11.899,16.088,13.707,26.401,13.481c6.741-0.148,13.48-0.148,20.22,0             c10.315,0.226,25.004-1.582,26.402-13.481c2.175-18.509,53.278,21.266,53.278,21.266l1.812-13.784l-30.267-18.239             C1435.994,1043.111,1431.069,1034.024,1431.069,1024.161z"/>
										</g>
										<g>
											<path style="fill:#050A10;" d="M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1394.681,1114.015c-3.973,0.348-8.085,0.557-12.126,0.557             c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069             v-13.379H1394.681z M1394.681,1114.015c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1480.813,1039.659             c-4.25-9.896-11.567-19.025-21.253-26.55c-0.418-0.279-0.838-0.628-1.256-0.976c-6.201-4.738-13.448-8.78-21.392-12.126             v-0.07c-15.751-6.69-34.357-10.661-54.356-10.661c-11.918,0-23.346,1.393-34.008,4.042             c-4.948,1.185-9.756,2.647-14.285,4.32c-1.534,0.557-3.067,1.115-4.531,1.742c-0.209,0-0.418,0.07-0.627,0.209             c-7.734,3.206-14.772,7.108-20.974,11.568c-6.969,5.157-12.893,10.802-17.353,17.143h-0.069             c-2.37,3.345-4.321,6.759-5.923,10.313c-2.858,6.412-4.322,13.101-4.322,20c0,12.404,4.74,24.112,13.171,34.147             l0.07,0.069c8.292,9.965,19.999,18.258,34.075,24.321c0.698,0.279,1.324,0.558,1.951,0.766             c1.464,0.627,2.997,1.255,4.531,1.812c0.905,0.348,1.81,0.628,2.717,0.977h0.069c3.554,1.184,7.108,2.23,10.872,3.135             h0.068c1.185,0.278,2.44,0.558,3.695,0.767c3.135,0.766,6.271,1.324,9.547,1.812h0.069             c2.996,0.417,5.924,0.835,9.129,0.975c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069             c3.066-0.14,6.062-0.487,9.128-0.975h0.072c1.463-0.209,2.786-0.418,4.319-0.697h0.069             c6.969-1.185,13.519-2.927,19.79-5.017c3.207-1.046,6.133-2.231,9.129-3.484h0.07             c14.146-6.132,25.783-14.496,34.147-24.391c8.431-10.104,13.239-21.812,13.239-34.216             C1484.715,1051.994,1483.392,1045.652,1480.813,1039.659z M1458.513,1090.182c-5.643,5.157-12.823,9.757-21.184,13.52             h-0.07c-0.14,0.069-0.347,0.139-0.556,0.208c-2.719,1.324-5.505,2.299-8.573,3.415             c-7.456,2.578-15.608,4.53-24.179,5.714h-0.072c-1.81,0.279-3.692,0.488-5.504,0.697h-0.069             c-1.185,0.14-2.371,0.28-3.555,0.28h-0.069c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557             c-3.205-0.14-6.133-0.489-9.129-0.907h-0.069c-2.022-0.278-3.973-0.626-5.923-0.975             c-1.047-0.209-2.093-0.418-3.067-0.628c-5.297-1.115-10.313-2.577-15.191-4.181h-0.069             c-0.907-0.349-1.812-0.697-2.717-0.976c-1.534-0.557-3.067-1.184-4.531-1.812c-0.627-0.279-1.253-0.557-1.951-0.905             c-8.362-3.693-15.47-8.223-21.115-13.52c-0.07,0-0.07,0-0.07-0.069c-4.389-3.902-7.804-8.224-10.104-12.892             c-0.069,0-0.069-0.07-0.069-0.07c-2.022-3.833-3.206-7.875-3.693-11.986v-0.07c-0.07-1.045-0.138-2.021-0.138-2.996             c0-7.806,2.997-15.401,8.292-22.092c1.184-1.393,2.16-2.647,3.552-4.041l0.072-0.069             c6.271-6.412,14.912-12.057,25.225-16.447c1.394-0.557,2.857-1.184,4.391-1.673c0.068,0,0.068,0,0.14-0.069             c4.738-1.742,9.895-3.345,15.261-4.529c0.279,0,0.486-0.07,0.766-0.139h0.069c10.036-2.231,20.767-3.555,32.196-3.555             c12.335,0,24.041,1.464,34.704,4.042h0.139c4.808,1.115,9.267,2.648,13.589,4.251h0.14             c4.321,1.602,8.431,3.415,12.195,5.436c9.269,5.017,16.794,11.08,21.743,17.98c0,0,0,0.069,0.069,0.069             c4.67,6.411,7.387,13.38,7.387,20.837c0,0.975,0,1.951-0.209,2.996v0.07c-0.349,4.111-1.533,8.153-3.624,11.986v0.07             c-2.368,4.599-5.783,8.99-10.104,12.961C1458.583,1090.112,1458.513,1090.112,1458.513,1090.182z M1394.681,1114.015             c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558             c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z"/>
										</g>
										<g style="opacity:0.33;">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000116957407722655955630000014252159191183384980_" filterUnits="userSpaceOnUse" x="1280.464" y="989.276" width="204.251" height="138.676">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1280.464" y="989.276" width="204.251" height="138.676" id="SVGID_00000026149612066757976970000015927075094731052982_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000116957407722655955630000014252159191183384980_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000031190494654433051670000013832709604646466237_" filterUnits="userSpaceOnUse" x="1280.464" y="989.276" width="204.251" height="138.676">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1280.464" y="989.276" width="204.251" height="138.676" id="SVGID_00000026149612066757976970000015927075094731052982_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000031190494654433051670000013832709604646466237_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000109028904233830168230000017550886557502900663_" gradientUnits="userSpaceOnUse" x1="1382.5898" y1="1127.9521" x2="1382.5898" y2="989.2764">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.028" style="stop-color:#EEEEEE"/>
														<stop offset="0.1037" style="stop-color:#C4C4C4"/>
														<stop offset="0.1811" style="stop-color:#A2A2A2"/>
														<stop offset="0.2597" style="stop-color:#888888"/>
														<stop offset="0.3397" style="stop-color:#757575"/>
														<stop offset="0.4222" style="stop-color:#6A6A6A"/>
														<stop offset="0.5108" style="stop-color:#666666"/>
														<stop offset="0.5714" style="stop-color:#6F6F6F"/>
														<stop offset="0.6716" style="stop-color:#888888"/>
														<stop offset="0.7989" style="stop-color:#B1B1B1"/>
														<stop offset="0.9468" style="stop-color:#E9E9E9"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000026149612066757976970000015927075094731052982_);fill:url(#SVGID_00000109028904233830168230000017550886557502900663_);" d="               M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558               c4.041,0,8.153-0.209,12.126-0.558v-13.379C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z                M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558               c4.041,0,8.153-0.209,12.126-0.558v-13.379C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z                M1394.681,1114.015c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379               c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1394.681,1114.015               c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379               c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1480.813,1039.659               c-4.25-9.896-11.567-19.025-21.253-26.55c-0.418-0.279-0.838-0.628-1.256-0.976               c-6.201-4.738-13.448-8.78-21.392-12.126v-0.07c-15.751-6.69-34.357-10.661-54.356-10.661               c-11.918,0-23.346,1.393-34.008,4.042c-4.948,1.185-9.756,2.647-14.285,4.32c-1.534,0.557-3.067,1.115-4.531,1.742               c-0.209,0-0.418,0.07-0.627,0.209c-7.734,3.206-14.772,7.108-20.974,11.568c-6.969,5.157-12.893,10.802-17.353,17.143               h-0.069c-2.37,3.345-4.321,6.759-5.923,10.313c-2.858,6.412-4.322,13.101-4.322,20c0,12.404,4.74,24.112,13.171,34.147               l0.07,0.069c8.292,9.965,19.999,18.258,34.075,24.321c0.698,0.279,1.324,0.558,1.951,0.766               c1.464,0.627,2.997,1.255,4.531,1.812c0.905,0.348,1.81,0.628,2.717,0.977h0.069c3.554,1.184,7.108,2.23,10.872,3.135               h0.068c1.185,0.278,2.44,0.558,3.695,0.767c3.135,0.766,6.271,1.324,9.547,1.812h0.069               c2.996,0.417,5.924,0.835,9.129,0.975c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069               c3.066-0.14,6.062-0.487,9.128-0.975h0.072c1.463-0.209,2.786-0.418,4.319-0.697h0.069               c6.969-1.185,13.519-2.927,19.79-5.017c3.207-1.046,6.133-2.231,9.129-3.484h0.07               c14.146-6.132,25.783-14.496,34.147-24.391c8.431-10.104,13.239-21.812,13.239-34.216               C1484.715,1051.994,1483.392,1045.652,1480.813,1039.659z M1458.513,1090.182c-5.643,5.157-12.823,9.757-21.184,13.52               h-0.07c-0.14,0.069-0.347,0.139-0.556,0.208c-2.719,1.324-5.505,2.299-8.573,3.415               c-7.456,2.578-15.608,4.53-24.179,5.714h-0.072c-1.81,0.279-3.692,0.488-5.504,0.697h-0.069               c-1.185,0.14-2.371,0.28-3.555,0.28h-0.069c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557               c-3.205-0.14-6.133-0.489-9.129-0.907h-0.069c-2.022-0.278-3.973-0.626-5.923-0.975               c-1.047-0.209-2.093-0.418-3.067-0.628c-5.297-1.115-10.313-2.577-15.191-4.181h-0.069               c-0.907-0.349-1.812-0.697-2.717-0.976c-1.534-0.557-3.067-1.184-4.531-1.812c-0.627-0.279-1.253-0.557-1.951-0.905               c-8.362-3.693-15.47-8.223-21.115-13.52c-0.07,0-0.07,0-0.07-0.069c-4.389-3.902-7.804-8.224-10.104-12.892               c-0.069,0-0.069-0.07-0.069-0.07c-2.022-3.833-3.206-7.875-3.693-11.986v-0.07c-0.07-1.045-0.138-2.021-0.138-2.996               c0-7.806,2.997-15.401,8.292-22.092c1.184-1.393,2.16-2.647,3.552-4.041l0.072-0.069               c6.271-6.412,14.912-12.057,25.225-16.447c1.394-0.557,2.857-1.184,4.391-1.673c0.068,0,0.068,0,0.14-0.069               c4.738-1.742,9.895-3.345,15.261-4.529c0.279,0,0.486-0.07,0.766-0.139h0.069c10.036-2.231,20.767-3.555,32.196-3.555               c12.335,0,24.041,1.464,34.704,4.042h0.139c4.808,1.115,9.267,2.648,13.589,4.251h0.14               c4.321,1.602,8.431,3.415,12.195,5.436c9.269,5.017,16.794,11.08,21.743,17.98c0,0,0,0.069,0.069,0.069               c4.67,6.411,7.387,13.38,7.387,20.837c0,0.975,0,1.951-0.209,2.996v0.07c-0.349,4.111-1.533,8.153-3.624,11.986v0.07               c-2.368,4.599-5.783,8.99-10.104,12.961C1458.583,1090.112,1458.513,1090.112,1458.513,1090.182z M1394.681,1114.015               c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379               c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1382.556,1114.572               c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558               v-13.379C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1382.556,1114.572               c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558               v-13.379C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000116936608607049608170000009230368569066344630_" gradientUnits="userSpaceOnUse" x1="1382.5898" y1="1127.9521" x2="1382.5898" y2="989.2764">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0102" style="stop-color:#F2F2F2"/>
												<stop offset="0.0685" style="stop-color:#BABABA"/>
												<stop offset="0.1286" style="stop-color:#898989"/>
												<stop offset="0.1892" style="stop-color:#5F5F5F"/>
												<stop offset="0.2504" style="stop-color:#3C3C3C"/>
												<stop offset="0.3125" style="stop-color:#222222"/>
												<stop offset="0.3757" style="stop-color:#0F0F0F"/>
												<stop offset="0.4408" style="stop-color:#040404"/>
												<stop offset="0.5108" style="stop-color:#000000"/>
												<stop offset="0.5523" style="stop-color:#090909"/>
												<stop offset="0.6209" style="stop-color:#222222"/>
												<stop offset="0.7081" style="stop-color:#4B4B4B"/>
												<stop offset="0.8105" style="stop-color:#848484"/>
												<stop offset="0.9244" style="stop-color:#CBCBCB"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000116936608607049608170000009230368569066344630_);" d="M1382.556,1114.572             c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558             v-13.379C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557             v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1394.681,1114.015c-3.973,0.348-8.085,0.557-12.126,0.557             c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069             v-13.379H1394.681z M1394.681,1114.015c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1480.813,1039.659             c-4.25-9.896-11.567-19.025-21.253-26.55c-0.418-0.279-0.838-0.628-1.256-0.976c-6.201-4.738-13.448-8.78-21.392-12.126             v-0.07c-15.751-6.69-34.357-10.661-54.356-10.661c-11.918,0-23.346,1.393-34.008,4.042             c-4.948,1.185-9.756,2.647-14.285,4.32c-1.534,0.557-3.067,1.115-4.531,1.742c-0.209,0-0.418,0.07-0.627,0.209             c-7.734,3.206-14.772,7.108-20.974,11.568c-6.969,5.157-12.893,10.802-17.353,17.143h-0.069             c-2.37,3.345-4.321,6.759-5.923,10.313c-2.858,6.412-4.322,13.101-4.322,20c0,12.404,4.74,24.112,13.171,34.147             l0.07,0.069c8.292,9.965,19.999,18.258,34.075,24.321c0.698,0.279,1.324,0.558,1.951,0.766             c1.464,0.627,2.997,1.255,4.531,1.812c0.905,0.348,1.81,0.628,2.717,0.977h0.069c3.554,1.184,7.108,2.23,10.872,3.135             h0.068c1.185,0.278,2.44,0.558,3.695,0.767c3.135,0.766,6.271,1.324,9.547,1.812h0.069             c2.996,0.417,5.924,0.835,9.129,0.975c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558h0.069             c3.066-0.14,6.062-0.487,9.128-0.975h0.072c1.463-0.209,2.786-0.418,4.319-0.697h0.069             c6.969-1.185,13.519-2.927,19.79-5.017c3.207-1.046,6.133-2.231,9.129-3.484h0.07             c14.146-6.132,25.783-14.496,34.147-24.391c8.431-10.104,13.239-21.812,13.239-34.216             C1484.715,1051.994,1483.392,1045.652,1480.813,1039.659z M1458.513,1090.182c-5.643,5.157-12.823,9.757-21.184,13.52             h-0.07c-0.14,0.069-0.347,0.139-0.556,0.208c-2.719,1.324-5.505,2.299-8.573,3.415             c-7.456,2.578-15.608,4.53-24.179,5.714h-0.072c-1.81,0.279-3.692,0.488-5.504,0.697h-0.069             c-1.185,0.14-2.371,0.28-3.555,0.28h-0.069c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557             c-3.205-0.14-6.133-0.489-9.129-0.907h-0.069c-2.022-0.278-3.973-0.626-5.923-0.975             c-1.047-0.209-2.093-0.418-3.067-0.628c-5.297-1.115-10.313-2.577-15.191-4.181h-0.069             c-0.907-0.349-1.812-0.697-2.717-0.976c-1.534-0.557-3.067-1.184-4.531-1.812c-0.627-0.279-1.253-0.557-1.951-0.905             c-8.362-3.693-15.47-8.223-21.115-13.52c-0.07,0-0.07,0-0.07-0.069c-4.389-3.902-7.804-8.224-10.104-12.892             c-0.069,0-0.069-0.07-0.069-0.07c-2.022-3.833-3.206-7.875-3.693-11.986v-0.07c-0.07-1.045-0.138-2.021-0.138-2.996             c0-7.806,2.997-15.401,8.292-22.092c1.184-1.393,2.16-2.647,3.552-4.041l0.072-0.069             c6.271-6.412,14.912-12.057,25.225-16.447c1.394-0.557,2.857-1.184,4.391-1.673c0.068,0,0.068,0,0.14-0.069             c4.738-1.742,9.895-3.345,15.261-4.529c0.279,0,0.486-0.07,0.766-0.139h0.069c10.036-2.231,20.767-3.555,32.196-3.555             c12.335,0,24.041,1.464,34.704,4.042h0.139c4.808,1.115,9.267,2.648,13.589,4.251h0.14             c4.321,1.602,8.431,3.415,12.195,5.436c9.269,5.017,16.794,11.08,21.743,17.98c0,0,0,0.069,0.069,0.069             c4.67,6.411,7.387,13.38,7.387,20.837c0,0.975,0,1.951-0.209,2.996v0.07c-0.349,4.111-1.533,8.153-3.624,11.986v0.07             c-2.368,4.599-5.783,8.99-10.104,12.961C1458.583,1090.112,1458.513,1090.112,1458.513,1090.182z M1394.681,1114.015             c-3.973,0.348-8.085,0.557-12.126,0.557c-4.112,0-8.154-0.14-12.125-0.557v13.379c3.971,0.349,8.014,0.558,12.125,0.558             c4.041,0,8.153-0.209,12.126-0.558h0.069v-13.379H1394.681z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z M1382.556,1114.572c-4.112,0-8.154-0.14-12.125-0.557v13.379             c3.971,0.349,8.014,0.558,12.125,0.558c4.041,0,8.153-0.209,12.126-0.558v-13.379             C1390.709,1114.363,1386.597,1114.572,1382.556,1114.572z"/>
										</g>
									</g>
									<g>
										<path style="fill:#05040C;" d="M1192.059,975.247c0,0.976-0.731,1.767-1.631,1.767h-31.906            c-0.902,0-1.632-0.792-1.632-1.767l0,0c0-0.976,0.731-1.766,1.632-1.766h31.906            C1191.328,973.481,1192.059,974.271,1192.059,975.247L1192.059,975.247z"/>
										<path style="fill:#05040C;" d="M1192.059,986.82c0,0.976-0.731,1.766-1.631,1.766h-31.906            c-0.902,0-1.632-0.791-1.632-1.766l0,0c0-0.976,0.731-1.766,1.632-1.766h31.906            C1191.328,985.054,1192.059,985.844,1192.059,986.82L1192.059,986.82z"/>
										<path style="fill:#05040C;" d="M1192.059,998.392c0,0.976-0.731,1.767-1.631,1.767h-31.906            c-0.902,0-1.632-0.791-1.632-1.767l0,0c0-0.976,0.731-1.766,1.632-1.766h31.906            C1191.328,996.626,1192.059,997.416,1192.059,998.392L1192.059,998.392z"/>
										<path style="fill:#05040C;" d="M1192.059,1009.965c0,0.976-0.731,1.766-1.631,1.766h-31.906            c-0.902,0-1.632-0.791-1.632-1.766l0,0c0-0.976,0.731-1.766,1.632-1.766h31.906            C1191.328,1008.199,1192.059,1008.989,1192.059,1009.965L1192.059,1009.965z"/>
									</g>
									<g>
										<path style="fill:#05040C;" d="M2255.301,975.247c0,0.976-0.73,1.767-1.631,1.767h-31.906            c-0.902,0-1.631-0.792-1.631-1.767l0,0c0-0.976,0.73-1.766,1.631-1.766h31.906            C2254.571,973.481,2255.301,974.271,2255.301,975.247L2255.301,975.247z"/>
										<path style="fill:#05040C;" d="M2255.301,986.82c0,0.976-0.73,1.766-1.631,1.766h-31.906            c-0.902,0-1.631-0.791-1.631-1.766l0,0c0-0.976,0.73-1.766,1.631-1.766h31.906            C2254.571,985.054,2255.301,985.844,2255.301,986.82L2255.301,986.82z"/>
										<path style="fill:#05040C;" d="M2255.301,998.392c0,0.976-0.73,1.767-1.631,1.767h-31.906            c-0.902,0-1.631-0.791-1.631-1.767l0,0c0-0.976,0.73-1.766,1.631-1.766h31.906            C2254.571,996.626,2255.301,997.416,2255.301,998.392L2255.301,998.392z"/>
										<path style="fill:#05040C;" d="M2255.301,1009.965c0,0.976-0.73,1.766-1.631,1.766h-31.906            c-0.902,0-1.631-0.791-1.631-1.766l0,0c0-0.976,0.73-1.766,1.631-1.766h31.906            C2254.571,1008.199,2255.301,1008.989,2255.301,1009.965L2255.301,1009.965z"/>
									</g>
									<g>
										<path style="fill:#000301;" d="M2282.345,943.91c-0.626-0.279-1.253-0.627-1.88-0.906            c-16.934-7.875-35.122-15.053-54.356-21.604c-2.787-0.975-5.644-1.951-8.501-2.856c-3.067-0.976-6.133-1.952-9.198-2.927            c-0.07,0-0.209-0.07-0.279-0.07c-6.482-2.021-13.032-4.042-19.722-5.924c-1.882-0.557-3.763-0.975-5.644-1.602            c-27.248-7.457-55.749-14.007-84.669-19.512c-2.926-0.627-5.853-1.184-8.85-1.673c-2.3-0.487-4.599-0.905-6.829-1.254            c-1.393-0.279-2.787-0.558-4.181-0.767c-0.976-0.139-1.882-0.348-2.858-0.487            c-68.153-12.056-137.421-19.233-197.003-23.484c-27.038-2.022-51.986-3.345-74.006-4.251            c-27.945-1.255-51.011-1.812-67.109-2.021c-16.167-0.209-25.365-0.209-25.365-0.209s-9.269,0-25.715,0.209            c-27.805,0.418-76.237,1.673-134.634,5.784c-2.161,0.139-4.391,0.279-6.552,0.488            c-14.077,0.976-28.709,2.161-43.693,3.484c-1.392,0.209-2.717,0.279-4.041,0.418            c-47.317,4.529-98.468,10.662-148.919,19.582c-3.277,0.556-6.622,1.115-9.897,1.742c-0.975,0.208-2.09,0.348-3.065,0.557            c-30.943,5.715-61.464,12.474-90.593,20.488c-1.742,0.348-3.275,0.767-4.947,1.325c-8.223,2.23-16.237,4.599-24.181,7.108            c-0.14,0-0.28,0.07-0.349,0.07c-3.135,0.975-6.203,1.951-9.269,2.927c-2.787,0.905-5.646,1.881-8.363,2.856            c-20,6.76-38.885,14.286-56.307,22.51c-0.626,0.348-1.393,0.766-2.09,1.254c-6.343,3.624-11.011,9.756-13.171,16.934            c-0.837,2.578-1.254,5.226-1.464,7.944c-0.069,1.673,0,3.415,0.14,5.226l0.486,4.391l4.81,46.341l29.966,287.386            c0.696,6.482,3.135,12.265,6.829,16.726c5.294,6.481,13.1,10.523,21.671,10.314l23.553-0.488l43.833-0.905l73.798-1.603            l4.531-0.069l188.5-3.972l189.13-4.112l64.807,1.394l23.415,0.488l239.025,5.087l50.383,1.115h1.812l57.143,1.254            l19.443,0.418l43.763,0.905l23.485,0.488c8.641,0.209,16.655-3.973,22.021-10.663c3.484-4.39,5.923-10.104,6.55-16.377            l13.658-130.87l6.76-65.296l9.478-90.942l3.414-32.543l1.463-14.077l0.418-4.391            C2300.255,962.098,2293.635,949.345,2282.345,943.91z M2282.276,973.248l-0.626,6.411l-3.484,33.031l-0.767,7.527            l-0.697,7.107l-0.767,7.595l-0.069,0.489l-0.14,0.627l-1.184,11.777l-0.14,1.463l-0.558,5.087l-2.16,20.836v0.069            l-4.32,41.394l-1.185,11.499l-0.418,3.972l-0.209,2.02l-1.672,16.308l-4.53,43.554v0.07l-0.836,7.804l-10.941,104.669            l-0.487,4.809c-0.07,0.418-0.14,0.767-0.208,1.185c-0.07,0.279-0.14,0.557-0.14,0.835l-0.069,0.14v0.07            c0,0.069,0,0.139-0.071,0.209c0,0.069,0,0.069,0,0.139c0,0,0,0.07,0,0.14c-0.069,0.07-0.139,0.14-0.139,0.14            c0,0.07-0.07,0.07-0.07,0.14c0,0.139-0.07,0.209-0.07,0.347c0,0-0.07,0.07-0.07,0.14s-0.07,0.14-0.07,0.209            c-0.14,0.418-0.349,0.697-0.557,1.046c0,0.069,0,0.069,0,0.139c-0.07,0.07-0.14,0.208-0.209,0.348v0.069l-0.14,0.07            c0,0.139-0.138,0.278-0.208,0.418h-0.07c-0.07,0.069-0.14,0.14-0.14,0.14c-0.14,0.279-0.349,0.488-0.558,0.767            c0.07,0.069,0,0.138-0.14,0.208l-0.069,0.069v0.07h-0.069c-0.07,0.069-0.139,0.139-0.208,0.278            c-0.07,0-0.07,0.069-0.14,0.209h-0.07l-0.07,0.069c0,0,0,0,0,0.07c0,0,0,0,0,0.07c0,0,0,0-0.069,0c0,0,0,0-0.069,0            c-0.07,0.069-0.07,0.069-0.139,0.14c0,0,0,0-0.07,0.069l-0.07,0.14c-0.07,0-0.14,0.069-0.209,0.14            c-0.07,0-0.14,0.07-0.14,0.139c-0.07,0-0.139,0-0.139,0.07c-0.07,0.069-0.07,0.069-0.07,0.069s0,0-0.069,0.069            c-0.14,0.14-0.348,0.28-0.557,0.418c-0.278,0.139-0.488,0.348-0.766,0.488c-0.07,0.14-0.209,0.14-0.28,0.209            c0,0.07-0.139,0.07-0.209,0.07c-0.278,0.139-0.627,0.279-0.975,0.418c-0.279,0.138-0.558,0.278-0.837,0.348            c-0.069,0-0.069,0-0.069,0v-0.07l-0.07,0.07c0,0.069-0.069,0-0.069,0c-0.07,0-0.07,0.069-0.07,0.069            c-0.209,0.069-0.488,0.139-0.627,0.139c-0.069,0.069-0.209,0.069-0.348,0.069c-0.21,0.07-0.558,0.14-0.837,0.07            c-0.14,0.07-0.279,0.14-0.417,0.07h-0.558c-0.07,0-0.07,0-0.14,0l-23.554-0.488l-6.898-0.14l-37.282-0.766v-0.069            l-22.927-0.488l-24.111-0.487l-9.199-0.209l-9.476-0.209l-9.129-0.14l-17.003-0.349l-31.081-0.696h-0.208l-5.994-0.139            l-18.258-0.418l-9.198-0.14l-18.119-0.418l-6.341-0.14l-57.631-1.184l-36.934-0.766l-31.429-0.696l-55.679-1.186            l-4.042-0.069l-2.02-0.069h-0.698l-0.976-0.07l-9.408-0.138l-2.438-0.07l-6.551-0.139h-0.28l-8.85-0.21l-6.83-0.139            l-8.641-0.14l-3.623-0.07l-5.226-0.14l-4.878-0.069h-0.069l-29.198-0.557l-111.08,2.3l-12.683,0.209l-25.715,0.558            l-15.402,0.347h-1.949l-3.833,0.07l-21.742,0.487l-25.157,0.558l-32.545,0.697h-0.695l-23.346,0.487l-9.199,0.209            l-20.209,0.419h-0.697l-2.089,0.069h-1.256l-3.412,0.069h-0.907l-4.808,0.14h-0.069l-24.251,0.488l-9.129,0.208h-0.069            l-8.85,0.209l-15.331,0.348l-3.554,0.07l-4.391,0.069h-1.324l-23.623,0.558h-0.069l-30.801,0.626l-17.354,0.349            l-9.267,0.209l-21.115,0.489l-13.868,0.278l-23.555,0.488h-1.254c-0.209-0.07-0.348-0.07-0.488-0.14            c-0.14,0-0.28,0-0.347-0.069c-0.209-0.07-0.349-0.07-0.558-0.139c-0.209,0-0.418,0-0.558-0.069            c-0.418-0.14-0.905-0.279-1.324-0.488c-0.209-0.07-0.418-0.209-0.627-0.349c-0.277-0.14-0.557-0.279-0.766-0.488            c-0.069-0.07-0.139-0.07-0.139-0.07v-0.07c-0.07,0-0.07,0-0.14-0.069c-0.07-0.069-0.14-0.069-0.209-0.139            c-0.07-0.069-0.209-0.209-0.349-0.28c-0.14-0.138-0.277-0.278-0.489-0.347c0-0.07-0.139-0.14-0.208-0.14            c-0.348-0.209-0.697-0.558-0.976-0.906c0,0-0.069-0.069-0.069-0.139v-0.07h-0.069c-0.208-0.14-0.347-0.348-0.419-0.557            c-0.139-0.069-0.208-0.139-0.139-0.209l-0.208-0.349c-0.07-0.069-0.14-0.209-0.209-0.279            c-0.07-0.07-0.14-0.208-0.21-0.348c-0.07-0.069-0.139-0.14-0.209-0.208c-0.139-0.14-0.209-0.278-0.209-0.488            c-0.069-0.07-0.069-0.07-0.069-0.21l-0.07-0.069c-0.073-0.132-0.108-0.287-0.175-0.424c-0.009-0.042,0-0.067-0.032-0.134            c-0.072-0.14-0.14-0.279-0.14-0.417c-0.072,0-0.072-0.07-0.072-0.07c-0.139-0.279-0.208-0.628-0.277-0.976l-0.069-0.07            c-0.07,0-0.07-0.07-0.07-0.14v-0.347c-0.07-0.07-0.07-0.208-0.07-0.348c-0.14-0.209-0.14-0.418-0.14-0.558            c-0.07-0.14-0.07-0.349-0.07-0.557c-0.07-0.069-0.07-0.14-0.07-0.209l-0.418-4.111v-0.07l-22.508-215.958l-3.066-29.268            l-1.324-13.311l-0.14-0.905l-1.395-13.032l-4.319-41.811v-0.208l-0.349-3.694l-0.975-9.337l-0.698-6.411            c-0.069-0.139-0.069-0.279-0.069-0.418v-0.744c0-0.831-0.046-1.672,0.139-2.462c0.07-0.14,0.07-0.349,0.14-0.558            c0-0.069,0-0.069,0-0.14c0-0.069,0-0.139,0.07-0.139c0.069-0.349,0.139-0.697,0.279-0.975c0-0.07,0.069-0.209,0.069-0.279            c0-0.069,0.07-0.209,0.14-0.279c0-0.045,0.024-0.114,0.044-0.179c0.229-0.699,0.542-1.375,0.951-2            c0.004-0.026,0-0.052,0.049-0.052c0.07-0.069,0.07-0.138,0.14-0.208v-0.07c0-0.07,0-0.07,0-0.07            c0.07,0,0.07-0.069,0.07-0.069c0.069-0.139,0.209-0.278,0.348-0.418c0.14-0.14,0.28-0.279,0.347-0.418            c0.211-0.209,0.279-0.279,0.418-0.489l0.07-0.069c0.139-0.139,0.209-0.208,0.349-0.348            c0.069-0.07,0.069-0.139,0.139-0.208c0.14-0.14,0.209-0.279,0.349-0.349h0.069c0-0.069,0-0.069,0.07-0.069            c0-0.07,0-0.07,0-0.07c0.069-0.139,0.14-0.209,0.277-0.209l0.072-0.07c0,0,0.068,0.07,0.068,0c0,0,0-0.069,0.071-0.069            l0.069-0.07c0.071-0.07,0.209-0.139,0.278-0.209c0-0.069,0.069-0.139,0.14-0.139c0.069-0.069,0.14-0.139,0.209-0.139            c0.069-0.07,0.14-0.14,0.279-0.209c0,0,0,0,0.07,0c0.279-0.208,0.558-0.348,0.837-0.488            c1.324-0.628,2.717-1.254,4.11-1.882c1.463-0.697,2.996-1.393,4.462-2.09c0.068,0,0.139,0,0.139,0            c0.068,0,0.068,0,0.068,0c0.14-0.07,0.21-0.14,0.279-0.14c0.698-0.348,1.395-0.627,2.091-0.975            c0.209-0.07,0.349-0.14,0.558-0.209c2.019-0.906,4.041-1.812,6.131-2.648c0.767-0.348,1.533-0.627,2.3-0.976            c1.045-0.488,2.091-0.906,3.066-1.324c0.14-0.069,0.349-0.069,0.489-0.14c0.209-0.07,0.486-0.139,0.697-0.279            c0.138-0.069,0.278-0.14,0.417-0.14c0.139-0.07,0.209-0.14,0.348-0.14c0.07-0.069,0.14-0.069,0.209-0.069            c0.349-0.209,0.696-0.348,1.045-0.487v-0.07c0.07,0,0.07,0,0.14,0c0,0,0.069,0,0.139,0c0.07-0.069,0.14-0.14,0.14-0.14            c0.349-0.138,0.696-0.208,1.115-0.417c0.349-0.139,0.697-0.279,1.116-0.418c2.089-0.836,4.18-1.602,6.34-2.438            c0.14-0.07,0.209-0.07,0.349-0.21c0.07,0,0.07,0,0.07,0.07c0.348-0.139,0.695-0.279,1.044-0.418            c0.837-0.279,1.673-0.628,2.509-0.976c0.486-0.208,1.115-0.418,1.602-0.627h0.07c0.349-0.069,0.696-0.209,1.114-0.348            c1.045-0.349,2.091-0.767,3.138-1.115c0.486-0.208,0.905-0.348,1.324-0.488c0.068,0,0.139-0.07,0.208-0.07            c0,0,0.071,0,0.139-0.069c0.209-0.07,0.349-0.14,0.558-0.21c0.628-0.209,1.256-0.488,1.951-0.696            c1.673-0.557,3.345-1.185,5.087-1.742c0.07,0,0.139-0.07,0.139-0.07c0.489-0.139,0.907-0.278,1.394-0.487            c0.209-0.14,0.418-0.14,0.627-0.209c0.349-0.07,0.695-0.209,1.115-0.349c0.209-0.069,0.488-0.14,0.767-0.209            c0.069-0.069,0.139-0.14,0.277-0.14c0.976-0.417,1.814-0.695,2.788-0.975c0.14,0,0.21-0.069,0.349-0.069            c0.766-0.279,1.603-0.558,2.44-0.767c0.347-0.139,0.696-0.279,1.044-0.348c0.07-0.069,0.209-0.069,0.347-0.14            c0.907-0.278,1.742-0.557,2.719-0.836c0.698-0.279,1.393-0.489,2.161-0.697c0.905-0.278,1.882-0.627,2.857-0.905            c0.07,0,0.209-0.07,0.28-0.07c0.069,0,0.139-0.07,0.209-0.07c0.209-0.07,0.349-0.14,0.558-0.209            c0.207-0.07,0.346-0.14,0.556-0.209c0.07-0.07,0.21-0.07,0.349-0.07c0.489-0.138,0.977-0.278,1.533-0.418            c0.209-0.069,0.418-0.138,0.628-0.208c0.557-0.139,1.114-0.349,1.672-0.488c1.742-0.558,3.415-1.046,5.157-1.533h0.14            c0.069-0.07,0.069-0.07,0.139-0.07c0.07,0,0.07-0.069,0.07-0.069c0.137-0.07,0.349-0.07,0.488-0.14            c0.068,0,0.208-0.069,0.278-0.069c0.418-0.14,0.906-0.209,1.393-0.418h0.14c0.07,0,0.07,0,0.07,0l0.069-0.07            c0.349-0.138,0.767-0.208,1.184-0.347h0.279c0.071-0.07,0.071-0.07,0.071-0.07c0.069-0.069,0.069-0.069,0.069-0.069            c0.209-0.07,0.488-0.14,0.837-0.209c0.279-0.14,0.558-0.14,0.837-0.209c0.347-0.14,0.696-0.209,0.975-0.279            c0.07,0,0.07,0,0.07,0s0,0,0.07,0v-0.069c0.069,0,0.277-0.07,0.349-0.07c0.068,0,0.068,0,0.068,0            c0.072-0.07,0.072-0.07,0.14-0.07l0.071-0.069h0.208c0,0,0.07-0.07,0.14-0.07c0.069-0.07,0.14-0.07,0.279-0.07h0.069            c0.07-0.07,0.209-0.07,0.279-0.07h0.07c0.069,0,0.139-0.07,0.279-0.07c0.068,0,0.068,0,0.068-0.069c0,0,0.072,0,0.14,0            c0.14,0,0.211,0,0.279-0.069c0.139-0.07,0.279-0.14,0.488-0.14c0,0,0-0.069,0.07-0.069l0.069-0.07            c2.37-0.627,4.738-1.325,7.108-1.951c0.139,0,0.209-0.069,0.349-0.14c0.418-0.07,0.904-0.279,1.323-0.348            c0.489-0.14,1.045-0.279,1.533-0.349c1.673-0.488,3.345-0.905,5.087-1.324c0.906-0.279,1.881-0.489,2.788-0.698            c0.417-0.138,0.766-0.208,1.115-0.278c0.556-0.208,1.044-0.348,1.533-0.488c0.14,0,0.349-0.069,0.558-0.139            c1.044-0.279,2.089-0.558,3.205-0.837c0.487-0.069,0.975-0.208,1.463-0.348c1.044-0.278,2.021-0.557,3.066-0.766            c0.139,0,0.279-0.07,0.418-0.07c0.14-0.069,0.279-0.14,0.418-0.14c0,0,0.07,0,0.14,0h0.069            c2.857-0.696,5.643-1.393,8.572-2.021c2.508-0.626,5.087-1.254,7.664-1.812c0.209-0.07,0.489-0.139,0.767-0.139            c1.882-0.488,3.764-0.906,5.646-1.325c0.626-0.069,1.184-0.209,1.812-0.349c0.068,0,0.14-0.069,0.208-0.069            c0.14-0.07,0.349-0.07,0.558-0.07c0.069,0,0.069-0.069,0.14-0.069c0.069-0.07,0.209-0.07,0.279-0.07            c0.489-0.14,1.044-0.208,1.533-0.348c1.533-0.279,3.066-0.628,4.599-0.977c0.209,0,0.349,0,0.558-0.07            c0.837-0.208,1.672-0.347,2.509-0.556c0.766-0.14,1.603-0.279,2.438-0.489c0.209,0,0.418-0.069,0.558-0.14            c0.209,0,0.418-0.07,0.626-0.07c0.35-0.07,0.767-0.209,1.116-0.279c0.28-0.07,0.626-0.14,0.905-0.21            c0.071-0.07,0.209-0.07,0.279-0.07c1.184-0.208,2.37-0.486,3.554-0.696c0.418-0.07,0.976-0.21,1.533-0.279            c0.767-0.209,1.534-0.349,2.3-0.488c0.765-0.14,1.533-0.279,2.3-0.418c0.347-0.139,0.765-0.209,1.114-0.279            c0.07,0,0.07,0,0.14,0c0.14-0.069,0.279-0.139,0.418-0.069c0.14-0.07,0.278-0.07,0.417-0.07c0.072,0,0.072,0,0.072,0            c0.905-0.209,1.81-0.418,2.786-0.628c1.464-0.279,2.926-0.558,4.39-0.836v-0.07h0.349c0.069,0,0.209,0,0.348-0.07            c0.28,0,0.558-0.069,0.838-0.139c0.486-0.07,0.975-0.209,1.463-0.279c0.906-0.14,1.882-0.349,2.857-0.558            c0.349-0.07,0.767-0.14,1.184-0.209c0.209-0.07,0.418-0.07,0.628-0.07c0.069,0,0.069,0,0.139,0c0.07,0,0.07,0,0.14-0.07            c0.279-0.069,0.558-0.139,0.835-0.139c0.349-0.07,0.698-0.139,1.047-0.139c1.672-0.348,3.414-0.697,5.087-0.906            c0.905-0.28,1.812-0.418,2.717-0.558c0.698-0.139,1.465-0.278,2.16-0.348c0.28-0.07,0.558-0.138,0.836-0.138v-0.07            c0.071,0,0.139,0,0.279-0.07c0.349,0,0.698-0.07,1.047-0.14c0.139,0,0.348-0.069,0.488-0.069            c1.393-0.279,2.787-0.489,4.18-0.698c0.349-0.069,0.767-0.14,1.114-0.209c0,0,0.072,0,0.14,0            c0.209-0.069,0.558-0.139,0.837-0.208c0.209-0.069,0.558-0.069,0.765-0.14c0.419-0.07,0.698-0.07,1.047-0.138            c0.349-0.07,0.695-0.14,1.044-0.21c0.419-0.07,0.698-0.139,1.047-0.139c0.279-0.07,0.626-0.07,0.975-0.14            c0.349-0.069,0.628-0.14,0.975-0.209c0.488-0.069,0.976-0.14,1.465-0.209c0.348-0.07,0.695-0.14,1.114-0.14            c0.07-0.069,0.14-0.069,0.28-0.069c0.837-0.14,1.602-0.279,2.368-0.348c1.395-0.278,2.788-0.488,4.182-0.697            c0.139-0.069,0.279-0.069,0.488-0.069c1.184-0.209,2.37-0.418,3.624-0.558c0.905-0.209,1.882-0.348,2.786-0.417            c3.694-0.557,7.318-1.115,11.011-1.673c0.14,0,0.21-0.069,0.21,0c2.786-0.418,5.713-0.905,8.501-1.254l1.672-0.209            c0.279-0.07,0.487-0.07,0.698-0.07c1.463-0.278,2.926-0.487,4.39-0.626c0.557-0.14,1.114-0.209,1.672-0.279            c0.558-0.07,1.047-0.14,1.533-0.209c0.838-0.14,1.673-0.21,2.44-0.279c0.069-0.07,0.069-0.07,0.14-0.07h0.068            c0.139,0,0.279-0.07,0.418-0.07c0.209,0,0.349-0.069,0.489-0.069c3.414-0.418,6.829-0.906,10.244-1.324            c0.209,0,0.418-0.07,0.628-0.07c0.069,0,0.14-0.07,0.209-0.07c0.626-0.069,1.254-0.139,1.882-0.209            c0.486-0.069,0.905-0.139,1.393-0.139c0.417-0.07,0.835-0.14,1.184-0.208c0.628-0.07,1.186-0.14,1.742-0.21            c0.07,0,0.14,0,0.279-0.07c0.558-0.069,1.115-0.139,1.603-0.139c0.558-0.07,1.114-0.139,1.602-0.209h0.349            c0.069,0,0.209,0,0.279-0.07c0.07,0.07,0.14,0,0.209,0c0-0.07,0.07-0.07,0.07-0.07c0.905-0.07,1.81-0.21,2.647-0.279            c0.768-0.14,1.533-0.209,2.231-0.279h0.627l0.069-0.07c1.813-0.209,3.623-0.418,5.365-0.627c0.28,0,0.489-0.07,0.698-0.07            c0.279-0.07,0.558-0.07,0.766-0.07c0.907-0.209,1.882-0.279,2.858-0.418c0.486-0.069,0.905-0.14,1.393-0.14            c0.767-0.069,1.464-0.139,2.161-0.209c0.417-0.07,0.905-0.139,1.324-0.139h0.209c0.07,0,0.07,0,0.14,0            c0.279-0.07,0.558-0.14,0.837-0.14c0.069,0,0.069,0,0.138,0c0.209,0,0.418-0.069,0.628-0.069            c6.83-0.766,13.589-1.464,20.278-2.161c0.14-0.07,0.279-0.07,0.349-0.07h0.698c0.069,0,0.069,0,0.139,0            c0.069,0,0.069,0,0.14,0c0.208,0,0.347-0.07,0.557-0.139c0.349,0,0.698-0.069,0.976-0.069            c2.019-0.209,3.901-0.418,5.852-0.558c0.418-0.069,0.838-0.069,1.254-0.14c0.418-0.07,0.837-0.07,1.255-0.07            c1.951-0.209,3.833-0.418,5.784-0.557c0,0,0,0,0.069-0.07h0.348c2.021-0.209,4.113-0.348,6.203-0.488v-0.07            c0.277,0,0.556,0,0.765-0.07c0.21-0.069,0.349-0.069,0.489-0.069h1.324c0.069,0,0.209-0.07,0.279-0.07            c0.07,0,0.07,0,0.07,0v-0.07c0.139,0,0.209,0,0.209,0s0.14,0,0.14-0.069c0,0.069,0,0.069,0,0.069h0.07            c0-0.069,0.069,0,0.069,0c0.209,0,0.347-0.069,0.626-0.069h0.072c1.324-0.14,2.648-0.209,4.041-0.349h0.697            c0.138-0.07,0.349-0.14,0.557-0.14c0.07,0,0.07,0,0.14,0h0.349c0.14-0.07,0.209-0.07,0.278-0.07            c0.767-0.069,1.533-0.139,2.231-0.208c0.557,0,1.044-0.07,1.463-0.14c3.764-0.278,7.527-0.557,11.151-0.836            c0.068-0.07,0.068,0,0.068,0v-0.07c0,0,0,0,0.07,0c0.558-0.07,1.116-0.07,1.672-0.14h0.349            c0.279-0.07,0.558-0.07,0.837-0.14h0.209c0.557,0,1.044-0.069,1.602-0.069c0.28,0,0.557-0.07,0.766-0.07            c0.418,0,0.767-0.069,1.044-0.069c3.835-0.278,7.597-0.557,11.221-0.835c0.139,0,0.209,0,0.209,0            c0-0.07,0.07-0.07,0.07-0.07s0.069,0,0.069,0.07c0.419-0.07,0.766-0.07,1.115-0.07c2.37-0.139,4.81-0.348,7.248-0.488            c0.209-0.07,0.418-0.07,0.558-0.07c1.742-0.139,3.484-0.209,5.226-0.348v-0.07h0.348c1.115-0.07,2.161-0.139,3.275-0.209            h1.115c0.558-0.07,1.116-0.07,1.673-0.139c0.697-0.069,1.325-0.069,1.882-0.139h1.046c0.626,0,1.254-0.07,1.742-0.07            c0.347,0,0.626-0.07,0.905-0.07c0.209-0.069,0.558-0.069,0.837-0.069c0.208-0.07,0.417-0.07,0.626-0.07h0.628            c0.209-0.07,0.417-0.07,0.556-0.07c0.14-0.069,0.209-0.069,0.209-0.069c0.349,0,0.767,0,1.116-0.07h0.417            c1.742-0.07,3.416-0.139,5.088-0.279c0.488,0,0.906-0.07,1.255-0.07c0.487,0,0.905-0.069,1.254-0.069            c0.418,0,0.835-0.069,1.254-0.069c0.489-0.07,0.907-0.07,1.324-0.07c0.418-0.069,0.767-0.069,1.116-0.069            c1.393-0.07,2.719-0.139,3.973-0.208c0.208,0,0.347-0.069,0.557-0.069h0.697c0.558-0.07,0.976-0.07,1.464-0.07            c0.279,0,0.557,0,0.767-0.07c1.742-0.07,3.414-0.139,5.087-0.279c0.557,0,1.115-0.069,1.602-0.069            c0.557-0.069,1.045-0.14,1.602-0.14h1.533c0.628-0.069,1.394-0.139,2.021-0.139h1.044c0.28,0,0.489-0.07,0.767-0.07            c0.279,0,0.627,0,0.975-0.069c0.558-0.069,1.185-0.069,1.812-0.069h2.857c0.14,0,0.349-0.07,0.488-0.07            c0.418-0.069,0.767-0.069,1.116-0.069c0.557-0.07,1.115-0.07,1.673-0.07h0.487c0.487-0.07,1.045-0.07,1.602-0.07            c0.07,0,0.07,0,0.14,0c0.208-0.069,0.348-0.069,0.557-0.069h0.349c0.07,0,0.07,0,0.07,0s0-0.07,0.069-0.07            c0,0,0,0.07,0.07,0.07c0.14,0,0.209,0,0.209,0c0.069-0.07,0.139-0.07,0.208-0.07h0.836            c1.603-0.07,3.275-0.139,4.808-0.208h1.047c0.069,0,0.139,0,0.139,0v-0.07h1.184c1.533-0.07,3.066-0.14,4.599-0.14            c0,0,0,0,0-0.07h0.767c3.484-0.139,6.899-0.208,10.105-0.348c0.418,0,0.767,0,1.185-0.069h1.115            c0.765,0,1.532-0.07,2.229-0.07c0.418,0,0.836,0,1.185-0.069c0.766,0,1.463-0.069,2.16-0.069c1.115,0,2.161,0,3.206-0.07            h0.488c0.836,0,1.672-0.069,2.438-0.069c0.205,0,0.452-0.017,0.681-0.034c0.413-0.012,0.832-0.023,1.24-0.035h1.703            c0.193-0.065,0.388-0.065,0.581-0.065c0.076-0.002,0.145-0.004,0.22-0.005h0.941c1.812,0,3.484-0.069,5.157-0.069            c1.463-0.07,2.787-0.07,4.181-0.14c1.882,0,3.693-0.069,5.435-0.069c0.697-0.07,1.395-0.07,2.021-0.07            c0.907-0.07,1.742-0.07,2.509-0.07c2.091-0.069,4.111-0.069,6.062-0.069h1.464c6.968-0.139,12.195-0.139,15.331-0.139            h6.83c2.16,0,5.226,0,9.198,0.069c1.395,0,2.927,0,4.53,0.069h3.205c0,0,0,0,0.07,0h5.087v0.069            c0.069,0.07,0.14,0.07,0.209,0c0.349,0.07,0.698,0.07,1.046,0.07h2.857c0.488,0.07,0.975,0.07,1.464,0.07            c0.069,0,0.069,0,0.14,0h4.878c0.069,0,0.069,0.069,0.069,0.069h2.648v0.07h0.906c0.488,0,0.976,0.07,1.533,0.07h2.37            c0.835,0,1.673,0,2.508,0.069h0.488c0.349,0,0.698,0.07,1.046,0.07c0.906,0,1.812,0,2.718,0.07c0.069,0,0.069,0,0.14,0            c1.184,0,2.369,0.069,3.554,0.14h3.972c0.417,0,0.766,0.069,1.185,0.069h0.209c0.626,0.069,1.324,0.069,1.951,0.069h0.697            c0.069,0,0.069,0.07,0.069,0.07h2.44c0.418,0,0.835,0,1.254,0.069c0.489,0,0.906,0.069,1.393,0            c0.698,0.069,1.464,0.069,2.16,0.069c0,0,0.07,0,0.07,0.07h0.836c1.602,0.07,3.205,0.138,4.878,0.208            c1.743,0.07,3.415,0.14,5.157,0.14c0.279,0.07,0.557,0.07,0.835,0.07h0.419l0.069,0.07c0,0,0,0,0.069,0h0.209            c0.418,0,0.905,0,1.254,0.069h1.185c0.488,0,0.976,0,1.463,0.069c1.394,0,2.787,0.07,4.181,0.14            c0.767,0.069,1.464,0.069,2.161,0.139c0.696,0,1.323,0,1.951,0.07h0.278c0.488,0,0.976,0,1.394,0.069            c1.115,0,2.23,0.07,3.345,0.14c0.627,0,1.184,0.069,1.812,0.14h1.811c0.348,0.069,0.628,0.069,0.975,0.069            c0.489,0.069,0.977,0.069,1.464,0.069c0.836,0,1.673,0,2.439,0.07c1.323,0.069,2.578,0.139,3.903,0.209            c0.07,0,0.14,0,0.209,0.069h0.348c0.417,0,0.836,0.069,1.185,0.069h0.905c0.14,0,0.279,0,0.419,0.07h0.628            c0.905,0.07,1.811,0.14,2.717,0.14c0.209,0,0.349,0,0.558,0.069h0.905c0,0,0,0,0.069,0.07h0.209            c2.021,0.069,4.042,0.208,6.133,0.279c0.767,0.069,1.603,0.139,2.369,0.209c1.394,0.07,2.719,0.14,4.042,0.209            c0.557,0,1.115,0.07,1.673,0.07c1.393,0.14,2.787,0.209,4.18,0.279c1.534,0.14,3.067,0.14,4.67,0.279            c0.417,0,0.905,0.069,1.393,0.069c0.139,0,0.278,0.069,0.418,0.069c0.766,0.07,1.533,0.07,2.369,0.14            c0.627,0.069,1.255,0.069,1.882,0.139h1.115c0.278,0,0.487,0.07,0.835,0.07c0.14,0,0.279,0.069,0.489,0.069            c0.07,0,0.07,0,0.139,0c0.488,0.069,1.045,0.069,1.533,0.069c0.349,0.07,0.697,0.07,1.115,0.14h0.348            c0.488,0,0.976,0,1.463,0.069c0.628,0.07,1.324,0.14,1.951,0.14c0.628,0.07,1.255,0.14,1.952,0.14            c1.115,0.07,2.23,0.209,3.414,0.279c0.349,0,0.698,0.07,1.046,0.07c0.418,0.07,0.836,0.07,1.254,0.07            c0.767,0.07,1.464,0.14,2.23,0.209c1.881,0.139,3.763,0.279,5.714,0.418c0.558,0.07,1.185,0.07,1.812,0.139h0.558            c0.348,0.069,0.627,0.069,0.905,0.069c1.115,0.07,2.09,0.14,3.136,0.21c0.279,0.07,0.627,0.07,0.905,0.139            c0.698,0.07,1.254,0.14,1.882,0.14c0.418,0.069,0.906,0.069,1.393,0.14h0.489c0.069,0,0.069,0,0.139,0.07h0.418            c0.279,0,0.627,0.069,0.905,0.069h0.906c0.279,0.07,0.487,0.07,0.766,0.07c0.14,0.07,0.349,0.07,0.489,0.07            c1.951,0.209,3.902,0.348,5.923,0.488c0.139,0,0.279,0,0.417,0.07c0.767,0,1.464,0.069,2.231,0.208            c1.324,0.14,2.648,0.209,3.971,0.349c1.186,0.069,2.37,0.209,3.624,0.279c0.418,0.07,0.837,0.14,1.255,0.14            c0.418,0.069,0.905,0.069,1.324,0.14c0.418,0.07,0.905,0.07,1.323,0.07c0.349,0.07,0.698,0.07,0.977,0.14            c1.184,0.139,2.369,0.208,3.623,0.278c1.254,0.14,2.508,0.279,3.693,0.348c0.417,0,0.837,0.069,1.325,0.14h0.766            c0.349,0.069,0.767,0.069,1.186,0.14c0.348,0.07,0.766,0.07,1.184,0.07l0.837,0.14l0.278,0.069c0,0,0,0,0.07,0            c0.837,0,1.812,0.14,2.719,0.209c0.557,0.07,1.184,0.14,1.812,0.209c0.557,0.07,1.184,0.14,1.812,0.209            c0.974,0.139,1.951,0.208,2.926,0.279c0.348,0,0.696,0.069,1.045,0.069c1.045,0.139,2.09,0.208,3.066,0.348            c0.696,0.14,1.464,0.209,2.16,0.209c0.279,0,0.627,0.07,0.906,0.07c0.14,0,0.279,0.07,0.417,0.07h0.349            c1.185,0.139,2.299,0.279,3.345,0.418c0.278,0.069,0.488,0.069,0.697,0.069c0.558,0.07,1.115,0.07,1.742,0.14            c0.836,0.139,1.672,0.139,2.439,0.278c0.418,0.07,0.836,0.07,1.254,0.14c0.209,0.07,0.418,0.07,0.627,0.139h0.418            c4.948,0.558,9.896,1.116,14.843,1.674h0.209c0.208,0.069,0.488,0.069,0.696,0.138c0.976,0.07,1.882,0.209,2.788,0.279            c0.975,0.139,1.882,0.279,2.857,0.348c0.767,0.07,1.603,0.21,2.37,0.279c0.418,0.14,0.975,0.14,1.464,0.14            c0.697,0.07,1.393,0.139,2.021,0.278c0.208,0.07,0.418,0.07,0.557,0.07h0.557c0.07,0.07,0.209,0.07,0.279,0.07            c1.254,0.208,2.578,0.348,3.833,0.487h0.069c2.16,0.349,4.25,0.628,6.411,0.907c1.673,0.208,3.344,0.418,5.087,0.626            c2.685,0.331,5.418,0.737,8.136,1.116c1.656,0.235,3.309,0.478,4.965,0.696c0.14,0.07,0.349,0.07,0.627,0.07            c0.349,0.07,0.628,0.14,0.907,0.14c0.07,0.069,0.208,0.069,0.279,0.069c1.881,0.279,3.693,0.558,5.644,0.767h0.279            c0.418,0.07,0.838,0.139,1.255,0.208c0.418,0,0.836,0.14,1.254,0.209h0.418c0.14,0,0.279,0,0.418,0.07            c0.139,0,0.139,0.069,0.279,0.069c0.069,0,0.069,0,0.069,0c0.07,0,0.07,0,0.14,0c0.07,0,0.14,0,0.209,0.07            c0.07,0,0.14,0.07,0.209,0.07c0.07,0,0.139,0,0.208,0.069h0.279c0.069,0,0.069,0,0.138,0            c1.255,0.209,2.58,0.418,3.833,0.558c0.07,0,0.14,0.069,0.279,0.069c0.07,0,0.14,0,0.209,0.07c0,0,0,0,0.07,0            c0.14,0,0.348,0,0.488,0.069c0.14,0,0.209,0,0.348,0.07l0.837,0.07c0.279,0.069,0.557,0.069,0.836,0.14            c0.139,0,0.279,0.069,0.488,0.069c0.14,0,0.279,0.07,0.418,0.07c0.07,0,0.07,0,0.14,0c0,0,0,0,0.069,0            c0.14,0.07,0.14,0.07,0.278,0.07c0.14,0.07,0.28,0.07,0.488,0.07h0.698c2.091,0.348,4.181,0.696,6.271,1.045            c0.837,0.14,1.742,0.28,2.58,0.419c0.627,0.069,1.253,0.208,1.881,0.347c1.323,0.208,2.647,0.417,3.972,0.628            c2.787,0.488,5.644,0.976,8.363,1.393c1.673,0.279,3.415,0.558,5.087,0.906c0.347,0.07,0.696,0.14,0.974,0.14            c0.349,0.07,0.698,0.139,1.046,0.208c0.487,0.07,0.976,0.14,1.394,0.209c0.279,0.07,0.557,0.07,0.835,0.139            c0.07,0,0.07,0,0.14,0.069c0.07,0,0.14,0,0.14,0c0.07,0,0.14,0,0.209,0.07c0.766,0.07,1.533,0.209,2.3,0.418            c2.229,0.349,4.529,0.766,6.76,1.184c1.324,0.209,2.717,0.418,4.042,0.767c1.533,0.279,3.067,0.557,4.6,0.836            c0,0,0.069,0,0.139,0l0.557,0.07c0.14,0.069,0.208,0.069,0.348,0.138c0,0,0,0,0.069,0c0.07,0,0.14,0,0.279,0.07            c0.069,0,0.069,0,0.14,0c0.905,0.209,1.881,0.349,2.787,0.558c3.484,0.696,6.899,1.324,10.384,2.021            c0.906,0.209,1.881,0.417,2.787,0.557c0.975,0.208,1.951,0.418,2.857,0.557c0.976,0.209,2.022,0.418,2.927,0.628            c1.046,0.279,2.021,0.488,2.997,0.627v0.07c0.557,0.069,1.115,0.208,1.672,0.348c0.14,0,0.21,0.069,0.28,0.069            c0.139,0,0.139,0,0.208,0c0.07,0,0.07,0,0.07,0c0.07,0,0.14,0,0.14,0c1.881,0.489,3.693,0.907,5.574,1.254            c0.767,0.208,1.464,0.348,2.23,0.488c1.533,0.349,3.066,0.698,4.669,1.045c0.348,0.07,0.697,0.209,1.046,0.209            c0.488,0.07,0.906,0.139,1.393,0.279c0.209,0.069,0.488,0.14,0.766,0.209c0.139,0.069,0.279,0.14,0.417,0.14            c0.28,0.069,0.489,0.139,0.768,0.139c0.348,0.07,0.696,0.14,1.045,0.21c0.279,0.069,0.558,0.139,0.767,0.209            c0.626,0.209,1.253,0.347,1.951,0.487c3.693,0.906,7.386,1.742,11.08,2.648c0,0,0.07,0,0.07-0.07h0.069v0.07l0.07,0.069            c0.07,0,0.07,0,0.07,0v-0.069c0,0,0,0,0.07,0c0.139,0.069,0.279,0.14,0.349,0.14c0.14,0,0.209,0,0.348,0.07            c0.139,0.069,0.348,0.069,0.487,0.14c0.14,0.069,0.349,0.139,0.558,0.139c0.418,0.07,0.836,0.208,1.254,0.279            c0.139,0,0.209,0.069,0.348,0.069c0.14,0.07,0.349,0.07,0.489,0.14c0.07,0,0.07,0,0.14,0.069c0.069,0,0.208,0,0.278,0.07            c0.14,0,0.209,0.069,0.349,0.139c0.627,0.14,1.254,0.349,1.881,0.489c0.069,0,0.069,0,0.069,0h0.07            c0.419,0.069,0.907,0.14,1.324,0.279c0.14,0.07,0.208,0.07,0.348,0.14c0.069,0.069,0.209,0.069,0.349,0.069            c0.07,0,0.14,0.07,0.209,0.07c0.14,0,0.279,0.069,0.418,0.069c0.418,0.07,0.975,0.209,1.324,0.349            c0.418,0.139,0.836,0.278,1.324,0.348c0.139,0,0.209,0.069,0.209,0.069c0.07,0,0.209,0.07,0.279,0.07            c0.349,0.07,0.628,0.14,0.906,0.21c0.348,0.139,0.767,0.209,1.185,0.279v0.069h0.07c0.417,0.14,0.765,0.209,1.184,0.348            c0.209,0.07,0.558,0.14,0.766,0.209c0.627,0.14,1.254,0.348,1.881,0.488c0.14,0.07,0.208,0.07,0.279,0.07            c0.349,0.069,0.697,0.14,0.977,0.279c0.278,0.069,0.557,0.139,0.905,0.209c0.489,0.139,1.046,0.279,1.603,0.487h0.279            c0.07,0,0.14,0.07,0.209,0.07c0.07,0,0.14,0.069,0.28,0.069c0.209,0,0.487,0.14,0.766,0.209            c2.02,0.557,4.042,1.115,6.133,1.742c2.09,0.557,4.111,1.115,6.132,1.742c0.348,0.069,0.697,0.208,1.046,0.348            c2.3,0.697,4.598,1.325,6.898,2.021c0.14,0.07,0.28,0.07,0.348,0.07c0.07,0.07,0.14,0.07,0.279,0.07            c0.557,0.14,1.115,0.349,1.672,0.489c0.628,0.138,1.115,0.347,1.673,0.556c0.487,0.14,1.045,0.349,1.602,0.489            c0.767,0.279,1.604,0.489,2.37,0.767c0.279,0.069,0.488,0.139,0.767,0.208c0.279,0.07,0.487,0.14,0.627,0.209            c0.278,0.069,0.487,0.139,0.696,0.208c0.07,0,0.07,0,0.14,0c0.07,0,0.07,0,0.07,0.07c1.184,0.418,2.438,0.767,3.623,1.185            c0.07,0,0.209,0.069,0.279,0.069v0.069c0.418,0.07,0.766,0.209,1.184,0.348c2.578,0.838,5.087,1.672,7.665,2.509            c0.418,0.14,0.836,0.28,1.254,0.418c1.324,0.488,2.578,0.906,3.833,1.325c0.418,0.209,0.835,0.349,1.254,0.488            c0.14,0.069,0.349,0.139,0.489,0.139c0.07,0,0.07,0,0.07,0c0.488,0.14,0.975,0.279,1.533,0.488            c0,0.069,0,0.069,0.07,0.069l0.07,0.07c1.812,0.628,3.554,1.254,5.226,1.881h0.07c0.139,0,0.139,0,0.208,0.07            c0.07,0,0.28,0.069,0.348,0.069h0.07c0.07,0,0.07,0,0.07,0.07c0.418,0.139,0.837,0.279,1.185,0.418h0.07v0.07            c0.14,0,0.21,0.069,0.28,0.139c0.069,0,0.139,0,0.208,0.07c0.07,0,0.209,0,0.279,0.07c0.14,0.07,0.28,0.139,0.419,0.208            c0.069,0,0.209,0.069,0.348,0.069c0.139,0.069,0.279,0.069,0.418,0.14c0,0,0,0,0.07,0.069            c0.069,0.07,0.208,0.07,0.278,0.14c0.767,0.279,1.603,0.628,2.371,0.905l0.07-0.069l0.069,0.069c0.07,0,0.14,0,0.21,0.07            h0.069c0.07,0,0.14,0.069,0.209,0.069c0.627,0.209,1.254,0.418,1.812,0.697c0.628,0.209,1.255,0.488,1.951,0.767            c0.419,0.138,0.837,0.348,1.324,0.556c0.071,0.07,0.14,0.07,0.209,0.07c0.07,0,0.14,0.069,0.14,0.069l0.697,0.209            c0.07,0.07,0.279,0.07,0.418,0.21c0.07,0,0.14,0,0.208,0.07c0.069,0.07,0.209,0.07,0.209,0.07h0.07            c0.07,0.07,0.07,0.07,0.14,0.14c0.069,0.07,0.139,0.07,0.208,0.07c0.14,0.07,0.279,0.14,0.349,0.14            c0.14,0,0.279,0.07,0.418,0.14c0.14,0.069,0.28,0.138,0.418,0.138h0.07c0,0,0,0,0.069,0.07            c0.14,0.069,0.348,0.069,0.557,0.209c0.349,0.07,0.698,0.278,0.907,0.418c0.208,0,0.347,0.069,0.556,0.209            c0.07,0,0.07,0,0.21,0.07c0.069,0,0.139,0.069,0.209,0.139c0.279,0.07,0.488,0.14,0.767,0.279            c0.069,0,0.138,0.069,0.208,0.069c0.209,0.07,0.557,0.209,0.906,0.348c0.489,0.209,0.976,0.418,1.533,0.627            c0.279,0.14,0.558,0.279,0.838,0.418c0.208,0.07,0.487,0.209,0.695,0.279c0.209,0.069,0.28,0.069,0.418,0.14            c0,0.07,0,0.07,0.07,0.07c0.07,0,0.07,0,0.07,0.07c0.07,0,0.07,0,0.07,0c0.557,0.208,1.114,0.487,1.603,0.696            c0.07,0,0.07,0,0.07,0c0.489,0.209,0.976,0.418,1.533,0.628c0.766,0.349,1.602,0.697,2.44,1.044            c0.208,0.07,0.417,0.21,0.695,0.349c0.209,0.14,0.419,0.209,0.628,0.279h0.07c0.069,0,0.069,0,0.139,0h0.07            c0.069,0.07,0.14,0.139,0.208,0.139v0.07c0.488,0.209,0.906,0.418,1.325,0.557c0.488,0.139,1.044,0.347,1.533,0.696            c0.976,0.418,1.951,0.837,2.926,1.254c0.349,0.209,0.698,0.349,1.116,0.558c0.348,0.209,0.766,0.349,1.115,0.488            c1.045,0.488,2.16,0.975,3.275,1.464c0.418,0.209,0.767,0.418,1.115,0.696c0.07,0,0.07,0,0.07,0s0.07,0,0.07,0.07            c0.279,0.139,0.488,0.348,0.697,0.557c0.209,0.139,0.278,0.209,0.418,0.348h0.07c0.07,0.14,0.279,0.14,0.348,0.349            c0.209,0.14,0.417,0.279,0.557,0.557l0.628,0.627c0.279,0.419,0.626,0.906,0.975,1.324            c0.28,0.489,0.558,1.047,0.767,1.604v0.069c0,0.07,0.07,0.209,0.07,0.279c0.209,0.279,0.279,0.489,0.347,0.837v0.208            c0.14,0.349,0.209,0.627,0.209,0.905c0,0.07,0.07,0.07,0.07,0.14c0,0.07,0.07,0.14,0.07,0.14            c0.07,0.279,0.07,0.488,0.139,0.766c0.069,0.627,0.14,1.254,0.14,1.882            C2282.416,972.342,2282.345,972.76,2282.276,973.248z M1505.412,877.151c0-0.069,0-0.14-0.069-0.14            c-2.578-3.972-5.227-8.014-8.083-12.265c-47.317,4.529-98.468,10.662-148.919,19.582            c-3.277,0.556-6.622,1.115-9.897,1.742c-0.975,0.208-2.09,0.348-3.065,0.557c-30.943,5.715-61.464,12.474-90.593,20.488            c-1.742,3.067-3.345,6.063-4.81,8.99v0.069c-0.486,0.838-0.975,1.674-1.392,2.371c-1.881,3.763-3.695,7.178-5.227,10.313            v0.069c0.068,0,0.208-0.069,0.278-0.069c0.418-0.14,0.906-0.209,1.393-0.418h0.14c0.07,0,0.07,0,0.07,0l0.069-0.07            c0.349-0.138,0.767-0.208,1.184-0.347h0.279c0.071-0.07,0.071-0.07,0.071-0.07c0.069-0.069,0.069-0.069,0.069-0.069            c0.209-0.07,0.488-0.14,0.837-0.209c0.279-0.14,0.558-0.14,0.837-0.209c0.347-0.14,0.696-0.209,0.975-0.279            c0.07,0,0.07,0,0.07,0s0,0,0.07,0v-0.069c0.069,0,0.277-0.07,0.349-0.07c0.068,0,0.068,0,0.068,0            c0.072-0.07,0.072-0.07,0.14-0.07l0.071-0.069h0.208c0,0,0.07-0.07,0.14-0.07c0.069-0.07,0.14-0.07,0.279-0.07h0.069            c0.07-0.07,0.209-0.07,0.279-0.07h0.07c0.069,0,0.139-0.07,0.279-0.07c0.068,0,0.068,0,0.068-0.069c0,0,0.072,0,0.14,0            c0.14,0,0.211,0,0.279-0.069c0.139-0.07,0.279-0.14,0.488-0.14c0,0,0-0.069,0.07-0.069l0.069-0.07            c2.37-0.627,4.738-1.325,7.108-1.951c0.139,0,0.209-0.069,0.349-0.14c0.418-0.07,0.904-0.279,1.323-0.348            c0.489-0.14,1.045-0.279,1.533-0.349c1.673-0.488,3.345-0.905,5.087-1.324c0.906-0.279,1.881-0.489,2.788-0.698            c0.417-0.138,0.766-0.208,1.115-0.278c0.556-0.208,1.044-0.348,1.533-0.488c0.14,0,0.349-0.069,0.558-0.139            c1.044-0.279,2.089-0.558,3.205-0.837c0.487-0.069,0.975-0.208,1.463-0.348c1.044-0.278,2.021-0.557,3.066-0.766            c0.139,0,0.279-0.07,0.418-0.07c0.14-0.069,0.279-0.14,0.418-0.14c0,0,0.07,0,0.14,0h0.069            c2.857-0.696,5.643-1.393,8.572-2.021c2.508-0.626,5.087-1.254,7.664-1.812c0.209-0.07,0.489-0.139,0.767-0.139            c1.882-0.488,3.764-0.906,5.646-1.325c0.626-0.069,1.184-0.209,1.812-0.349c0.068,0,0.14-0.069,0.208-0.069            c0.14-0.07,0.349-0.07,0.558-0.07c0.069,0,0.069-0.069,0.14-0.069c0.069-0.07,0.209-0.07,0.279-0.07            c0.489-0.14,1.044-0.208,1.533-0.348c1.533-0.279,3.066-0.628,4.599-0.977c0.209,0,0.349,0,0.558-0.07            c0.837-0.208,1.672-0.347,2.509-0.556c0.766-0.14,1.603-0.279,2.438-0.489c0.209,0,0.418-0.069,0.558-0.14            c0.209,0,0.418-0.07,0.626-0.07c0.35-0.07,0.767-0.209,1.116-0.279c0.28-0.07,0.626-0.14,0.905-0.21            c0.071-0.07,0.209-0.07,0.279-0.07c1.184-0.208,2.37-0.486,3.554-0.696c0.418-0.07,0.976-0.21,1.533-0.279            c0.767-0.209,1.534-0.349,2.3-0.488c0.765-0.14,1.533-0.279,2.3-0.418c0.347-0.139,0.765-0.209,1.114-0.279            c0.07,0,0.07,0,0.14,0c0.14-0.069,0.279-0.139,0.418-0.069c0.14-0.07,0.278-0.07,0.417-0.07c0.072,0,0.072,0,0.072,0            c0.905-0.209,1.81-0.418,2.786-0.628c1.464-0.279,2.926-0.558,4.39-0.836v-0.07h0.349c0.069,0,0.209,0,0.348-0.07            c0.28,0,0.558-0.069,0.838-0.139c0.486-0.07,0.975-0.209,1.463-0.279c0.906-0.14,1.882-0.349,2.857-0.558            c0.349-0.07,0.767-0.14,1.184-0.209c0.209-0.07,0.418-0.07,0.628-0.07c0.069,0,0.069,0,0.139,0c0.07,0,0.07,0,0.14-0.07            c0.279-0.069,0.558-0.139,0.835-0.139c0.349-0.07,0.698-0.139,1.047-0.139c1.672-0.348,3.414-0.697,5.087-0.906            c0.905-0.28,1.812-0.418,2.717-0.558c0.698-0.139,1.465-0.278,2.16-0.348c0.28-0.07,0.558-0.138,0.836-0.138v-0.07            c0.071,0,0.139,0,0.279-0.07c0.349,0,0.698-0.07,1.047-0.14c0.139,0,0.348-0.069,0.488-0.069            c1.393-0.279,2.787-0.489,4.18-0.698c0.349-0.069,0.767-0.14,1.114-0.209c0,0,0.072,0,0.14,0            c0.209-0.069,0.558-0.139,0.837-0.208c0.209-0.069,0.558-0.069,0.765-0.14c0.419-0.07,0.698-0.07,1.047-0.138            c0.349-0.07,0.695-0.14,1.044-0.21c0.419-0.07,0.698-0.139,1.047-0.139c0.279-0.07,0.626-0.07,0.975-0.14            c0.349-0.069,0.628-0.14,0.975-0.209c0.488-0.069,0.976-0.14,1.465-0.209c0.348-0.07,0.695-0.14,1.114-0.14            c0.07-0.069,0.14-0.069,0.28-0.069c0.837-0.14,1.602-0.279,2.368-0.348c1.395-0.278,2.788-0.488,4.182-0.697            c0.139-0.069,0.279-0.069,0.488-0.069c1.184-0.209,2.37-0.418,3.624-0.558c0.905-0.209,1.882-0.348,2.786-0.417            c3.694-0.557,7.318-1.115,11.011-1.673c0.14,0,0.21-0.069,0.21,0c2.786-0.418,5.713-0.905,8.501-1.254l1.672-0.209            c0.279-0.07,0.487-0.07,0.698-0.07c1.463-0.278,2.926-0.487,4.39-0.626c0.557-0.14,1.114-0.209,1.672-0.279            c0.558-0.07,1.047-0.14,1.533-0.209c0.838-0.14,1.673-0.21,2.44-0.279c0.069-0.07,0.069-0.07,0.14-0.07h0.068            c0.139,0,0.279-0.07,0.418-0.07c0.209,0,0.349-0.069,0.489-0.069c3.414-0.418,6.829-0.906,10.244-1.324            c0.209,0,0.418-0.07,0.628-0.07c0.069,0,0.14-0.07,0.209-0.07c0.626-0.069,1.254-0.139,1.882-0.209            c0.486-0.069,0.905-0.139,1.393-0.139c0.417-0.07,0.835-0.14,1.184-0.208c0.628-0.07,1.186-0.14,1.742-0.21            c0.07,0,0.14,0,0.279-0.07c0.558-0.069,1.115-0.139,1.603-0.139c0.558-0.07,1.114-0.139,1.602-0.209h0.349            c0.069,0,0.209,0,0.279-0.07c0.07,0.07,0.14,0,0.209,0c0-0.07,0.07-0.07,0.07-0.07c0.905-0.07,1.81-0.21,2.647-0.279            c0.768-0.14,1.533-0.209,2.231-0.279h0.627l0.069-0.07c1.813-0.209,3.623-0.418,5.365-0.627c0.28,0,0.489-0.07,0.698-0.07            c0.279-0.07,0.558-0.07,0.766-0.07c0.907-0.209,1.882-0.279,2.858-0.418c0.486-0.069,0.905-0.14,1.393-0.14            c0.767-0.069,1.464-0.139,2.161-0.209c0.417-0.07,0.905-0.139,1.324-0.139h0.209c0.07,0,0.07,0,0.14,0            c0.279-0.07,0.558-0.14,0.837-0.14c0.069,0,0.069,0,0.138,0c0.209,0,0.418-0.069,0.628-0.069            c6.83-0.766,13.589-1.464,20.278-2.161c0.14-0.07,0.279-0.07,0.349-0.07h0.698c0.069,0,0.069,0,0.139,0            c0.069,0,0.069,0,0.14,0c0.208,0,0.347-0.07,0.557-0.139c0.349,0,0.698-0.069,0.976-0.069            c2.019-0.209,3.901-0.418,5.852-0.558c0.418-0.069,0.838-0.069,1.254-0.14c0.418-0.07,0.837-0.07,1.255-0.07            c1.951-0.209,3.833-0.418,5.784-0.557c0,0,0,0,0.069-0.07h0.348c2.021-0.209,4.113-0.348,6.203-0.488v-0.07            c0.277,0,0.556,0,0.765-0.07c0.21-0.069,0.349-0.069,0.489-0.069h1.324c0.069,0,0.209-0.07,0.279-0.07            c0.07,0,0.07,0,0.07,0v-0.07c0.139,0,0.209,0,0.209,0s0.14,0,0.14-0.069c0,0.069,0,0.069,0,0.069h0.07            c0-0.069,0.069,0,0.069,0c0.209,0,0.347-0.069,0.626-0.069v-0.069C1507.573,880.217,1506.459,878.753,1505.412,877.151z"/>
										<g>
											<g style="opacity:0.54;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000016035791664441627410000006552809446129319345_" filterUnits="userSpaceOnUse" x="1141.242" y="872.54" width="1141.058" height="449.815">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1141.242" y="872.54" width="1141.058" height="449.815" id="SVGID_00000101790820475738286950000008009186410031987108_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000016035791664441627410000006552809446129319345_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000080888056066927341190000000203027256906569142_" filterUnits="userSpaceOnUse" x="1141.242" y="872.54" width="1141.058" height="449.815">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1141.242" y="872.54" width="1141.058" height="449.815" id="SVGID_00000101790820475738286950000008009186410031987108_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000080888056066927341190000000203027256906569142_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000068671948186290349470000016034203054228058011_" gradientUnits="userSpaceOnUse" x1="1141.2419" y1="1097.4479" x2="2282.2998" y2="1097.4479">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0575" style="stop-color:#F0F0F0"/>
															<stop offset="0.1676" style="stop-color:#CACACA"/>
															<stop offset="0.3174" style="stop-color:#8C8C8C"/>
															<stop offset="0.457" style="stop-color:#4D4D4D"/>
															<stop offset="0.517" style="stop-color:#565656"/>
															<stop offset="0.6162" style="stop-color:#6F6F6F"/>
															<stop offset="0.7423" style="stop-color:#989898"/>
															<stop offset="0.8887" style="stop-color:#D0D0D0"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000101790820475738286950000008009186410031987108_);fill:url(#SVGID_00000068671948186290349470000016034203054228058011_);" d="                M2282.227,973.312l-35.189,338.055c-0.67,6.279-5.577,10.988-11.376,10.988l-242.415-5.131l-192.056-4.106                l-60.449-1.268l-29.278-0.604l-217.543,4.589l-305.819,6.52c-3.401,0-6.47-1.509-8.531-3.984                c-1.674-1.873-2.733-4.287-3.011-7.005l-33.182-318.493l-2.064-19.562c-0.556-5.435,2.118-10.567,6.693-12.74                c180.792-85.556,522.022-88.032,560.333-88.032h6.859c2.118,0,5.187,0,9.146,0.061                c29.165,0.181,105.563,1.57,196.628,9.903c117.165,10.626,258.528,32.604,354.559,78.069                C2280.105,962.745,2282.785,967.877,2282.227,973.312z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000116950452950039471850000013349146930659041179_" gradientUnits="userSpaceOnUse" x1="1141.2419" y1="1097.4479" x2="2282.2998" y2="1097.4479">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0475" style="stop-color:#EEF4F9"/>
													<stop offset="0.1384" style="stop-color:#C8DDEE"/>
													<stop offset="0.2634" style="stop-color:#89B7DC"/>
													<stop offset="0.4163" style="stop-color:#3483C3"/>
													<stop offset="0.457" style="stop-color:#1C75BC"/>
													<stop offset="0.5072" style="stop-color:#257BBF"/>
													<stop offset="0.5902" style="stop-color:#3E8AC6"/>
													<stop offset="0.6957" style="stop-color:#67A3D2"/>
													<stop offset="0.8195" style="stop-color:#A0C5E3"/>
													<stop offset="0.9573" style="stop-color:#E7F1F8"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000116950452950039471850000013349146930659041179_);" d="M2282.227,973.312              l-35.189,338.055c-0.67,6.279-5.577,10.988-11.376,10.988l-242.415-5.131l-192.056-4.106l-60.449-1.268l-29.278-0.604              l-217.543,4.589l-305.819,6.52c-3.401,0-6.47-1.509-8.531-3.984c-1.674-1.873-2.733-4.287-3.011-7.005l-33.182-318.493              l-2.064-19.562c-0.556-5.435,2.118-10.567,6.693-12.74c180.792-85.556,522.022-88.032,560.333-88.032h6.859              c2.118,0,5.187,0,9.146,0.061c29.165,0.181,105.563,1.57,196.628,9.903c117.165,10.626,258.528,32.604,354.559,78.069              C2280.105,962.745,2282.785,967.877,2282.227,973.312z"/>
											</g>
											<path style="opacity:0.08;fill:#FFFFFF;" d="M2282.227,973.312l-35.189,338.055c-0.67,6.279-5.577,10.988-11.376,10.988             l-242.415-5.131l-192.056-4.106c8.03-175.277-22.807-320.485-76.844-440.517c29.165,0.181,105.563,1.57,196.628,9.903             c117.165,10.626,258.528,32.604,354.559,78.069C2280.105,962.745,2282.785,967.877,2282.227,973.312z"/>
											<path style="opacity:0.08;fill:#FFFFFF;" d="M2282.227,973.312l-35.189,338.055c-0.67,6.279-5.577,10.988-11.376,10.988             l-242.415-5.131c8.423-172.318-20.577-315.655-72.272-434.72c117.165,10.626,258.528,32.604,354.559,78.069             C2280.105,962.745,2282.785,967.877,2282.227,973.312z"/>
											<path style="opacity:0.05;fill:#FFFFFF;" d="M1493.921,1315.835l-305.819,6.52c-3.401,0-6.47-1.509-8.531-3.984             c-1.674-1.873-2.733-4.287-3.011-7.005l-33.182-318.493h1.787c118.164,0,222.893,65.813,287.748,167.247             C1462.136,1205.766,1483.271,1258.597,1493.921,1315.835z"/>
											<path style="opacity:0.05;fill:#FFFFFF;" d="M1740.741,1311.85l-29.278-0.604l-217.543,4.589l-305.819,6.52             c-3.401,0-6.47-1.509-8.531-3.984c59.556-88.695,150.232-148.591,253.342-158.251c9.705-0.965,19.576-1.389,29.5-1.389             C1575.004,1158.732,1675.382,1218.506,1740.741,1311.85z"/>
										</g>
										<g>
											<g>
												<path style="fill:#090C09;" d="M1693.86,916.495c-1.286-0.031-84.709-2.139-178.802,4.146              c-2.025-3.516-5.658-5.804-9.751-5.593l-23.865,1.228c-1.258,0.065-2.434,0.403-3.54,0.884l-83.297-12.409              c-1.506-0.222-2.87,0.91-3.08,2.526c-0.204,1.618,0.84,3.11,2.333,3.331l78.601,11.709              c-0.3,0.578-0.573,1.172-0.78,1.803c-36.759,3.487-73.49,8.449-106.143,15.478c-2.959,0.637-4.879,3.75-4.291,6.952              c0.512,2.782,2.748,4.717,5.265,4.76c0.381,0.007,0.768-0.03,1.156-0.114c32.592-7.016,69.32-11.951,106.066-15.411              c2.118,2.65,5.263,4.292,8.746,4.113l23.865-1.228c3.967-0.204,7.303-2.696,9.063-6.221              c93.643-6.274,176.893-4.173,178.18-4.133c0.016,0,0.034,0.001,0.051,0.001c2.994,0.051,5.469-2.518,5.546-5.763              C1699.256,919.289,1696.874,916.577,1693.86,916.495z"/>
												<g>
													<g>
														<path style="fill:#070709;" d="M1373.134,955.714c-2.517-0.043-4.754-1.978-5.265-4.76                c-0.588-3.203,1.333-6.315,4.291-6.952c130.569-28.107,326.349-23.152,328.323-23.104                c3.015,0.082,5.397,2.794,5.323,6.059c-0.077,3.246-2.552,5.814-5.546,5.763c-0.016,0-0.035-0.001-0.051-0.001                c-1.973-0.061-196.543-4.972-325.92,22.88C1373.902,955.684,1373.515,955.721,1373.134,955.714z"/>
													</g>
													<g>
														<path style="fill:#030303;" d="M1524.117,933.407c-0.109-0.001-0.221-0.011-0.333-0.027l-123.301-18.368                c-1.493-0.222-2.538-1.713-2.333-3.331c0.21-1.616,1.574-2.747,3.079-2.526l123.301,18.368                c1.493,0.221,2.538,1.714,2.333,3.331C1526.671,932.351,1525.479,933.43,1524.117,933.407z"/>
													</g>
													<g>
														<g style="opacity:0.22;">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000003071012373719225460000000272456367489681797_" filterUnits="userSpaceOnUse" x="1367.764" y="920.532" width="338.045" height="35.182">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="1367.764" y="920.532" width="338.045" height="35.182" id="SVGID_00000160895371783529516360000010516763076759725961_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000003071012373719225460000000272456367489681797_);">
																	<defs>
																		
																			<filter id="Adobe_OpacityMaskFilter_00000064322234776026803160000008231130572551127737_" filterUnits="userSpaceOnUse" x="1367.764" y="920.532" width="338.045" height="35.182">
																			<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																			<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																		</filter>
																	</defs>
																	
																		<mask maskUnits="userSpaceOnUse" x="1367.764" y="920.532" width="338.045" height="35.182" id="SVGID_00000160895371783529516360000010516763076759725961_">
																		<g style="filter:url(#Adobe_OpacityMaskFilter_00000064322234776026803160000008231130572551127737_);">
																		</g>
																	</mask>
																	
																		<linearGradient id="SVGID_00000067919392413767319110000002897255411141068197_" gradientUnits="userSpaceOnUse" x1="1345.8909" y1="975.0178" x2="1683.5236" y2="975.0178" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
																		<stop offset="0" style="stop-color:#FFFFFF"/>
																		<stop offset="0.028" style="stop-color:#EEEEEE"/>
																		<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																		<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																		<stop offset="0.2597" style="stop-color:#888888"/>
																		<stop offset="0.3397" style="stop-color:#757575"/>
																		<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																		<stop offset="0.5108" style="stop-color:#666666"/>
																		<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																		<stop offset="0.6716" style="stop-color:#888888"/>
																		<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																		<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																		<stop offset="1" style="stop-color:#FFFFFF"/>
																	</linearGradient>
																	
																		<path style="mask:url(#SVGID_00000160895371783529516360000010516763076759725961_);fill:url(#SVGID_00000067919392413767319110000002897255411141068197_);" d="                   M1373.134,955.714c-2.517-0.043-4.754-1.978-5.265-4.76c-0.588-3.203,1.333-6.315,4.291-6.952                   c130.569-28.107,326.349-23.152,328.323-23.104c3.015,0.082,5.397,2.794,5.323,6.059                   c-0.077,3.246-2.552,5.814-5.546,5.763c-0.016,0-0.035-0.001-0.051-0.001c-1.973-0.061-196.543-4.972-325.92,22.88                   C1373.902,955.684,1373.515,955.721,1373.134,955.714z"/>
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000161623639784184788020000015943476851512253317_" gradientUnits="userSpaceOnUse" x1="1345.8909" y1="975.0178" x2="1683.5236" y2="975.0178" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
																<stop offset="0" style="stop-color:#FDFDFD"/>
																<stop offset="0.0102" style="stop-color:#F2F2F2"/>
																<stop offset="0.0685" style="stop-color:#BABABA"/>
																<stop offset="0.1286" style="stop-color:#898989"/>
																<stop offset="0.1892" style="stop-color:#5F5F5F"/>
																<stop offset="0.2504" style="stop-color:#3C3C3C"/>
																<stop offset="0.3125" style="stop-color:#222222"/>
																<stop offset="0.3757" style="stop-color:#0F0F0F"/>
																<stop offset="0.4408" style="stop-color:#040404"/>
																<stop offset="0.5108" style="stop-color:#000000"/>
																<stop offset="0.5523" style="stop-color:#090909"/>
																<stop offset="0.6209" style="stop-color:#222222"/>
																<stop offset="0.7081" style="stop-color:#4B4B4B"/>
																<stop offset="0.8105" style="stop-color:#848484"/>
																<stop offset="0.9244" style="stop-color:#CBCBCB"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															<path style="fill:url(#SVGID_00000161623639784184788020000015943476851512253317_);" d="M1373.134,955.714                 c-2.517-0.043-4.754-1.978-5.265-4.76c-0.588-3.203,1.333-6.315,4.291-6.952                 c130.569-28.107,326.349-23.152,328.323-23.104c3.015,0.082,5.397,2.794,5.323,6.059                 c-0.077,3.246-2.552,5.814-5.546,5.763c-0.016,0-0.035-0.001-0.051-0.001c-1.973-0.061-196.543-4.972-325.92,22.88                 C1373.902,955.684,1373.515,955.721,1373.134,955.714z"/>
														</g>
													</g>
													<g>
														<g style="opacity:0.22;">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000154415526843232966240000016111747522538626227_" filterUnits="userSpaceOnUse" x="1398.124" y="909.126" width="128.765" height="24.281">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="1398.124" y="909.126" width="128.765" height="24.281" id="SVGID_00000151522505927586036250000013091774193423662766_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000154415526843232966240000016111747522538626227_);">
																	<defs>
																		
																			<filter id="Adobe_OpacityMaskFilter_00000168098719884737414700000013410209190527062417_" filterUnits="userSpaceOnUse" x="1398.124" y="909.126" width="128.765" height="24.281">
																			<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																			<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																		</filter>
																	</defs>
																	
																		<mask maskUnits="userSpaceOnUse" x="1398.124" y="909.126" width="128.765" height="24.281" id="SVGID_00000151522505927586036250000013091774193423662766_">
																		<g style="filter:url(#Adobe_OpacityMaskFilter_00000168098719884737414700000013410209190527062417_);">
																		</g>
																	</mask>
																	
																		<linearGradient id="SVGID_00000036228962468970366150000014781232767186642360_" gradientUnits="userSpaceOnUse" x1="1375.6516" y1="959.169" x2="1504.688" y2="959.169" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
																		<stop offset="0" style="stop-color:#FFFFFF"/>
																		<stop offset="0.028" style="stop-color:#EEEEEE"/>
																		<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																		<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																		<stop offset="0.2597" style="stop-color:#888888"/>
																		<stop offset="0.3397" style="stop-color:#757575"/>
																		<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																		<stop offset="0.5108" style="stop-color:#666666"/>
																		<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																		<stop offset="0.6716" style="stop-color:#888888"/>
																		<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																		<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																		<stop offset="1" style="stop-color:#FFFFFF"/>
																	</linearGradient>
																	
																		<path style="mask:url(#SVGID_00000151522505927586036250000013091774193423662766_);fill:url(#SVGID_00000036228962468970366150000014781232767186642360_);" d="                   M1524.117,933.407c-0.109-0.001-0.221-0.011-0.333-0.027l-123.301-18.368c-1.493-0.222-2.538-1.713-2.333-3.331                   c0.21-1.616,1.574-2.747,3.079-2.526l123.301,18.368c1.493,0.221,2.538,1.714,2.333,3.331                   C1526.671,932.351,1525.479,933.43,1524.117,933.407z"/>
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000163038844336021037350000012567685641694802068_" gradientUnits="userSpaceOnUse" x1="1375.6516" y1="959.169" x2="1504.688" y2="959.169" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
																<stop offset="0" style="stop-color:#FDFDFD"/>
																<stop offset="0.0102" style="stop-color:#F2F2F2"/>
																<stop offset="0.0685" style="stop-color:#BABABA"/>
																<stop offset="0.1286" style="stop-color:#898989"/>
																<stop offset="0.1892" style="stop-color:#5F5F5F"/>
																<stop offset="0.2504" style="stop-color:#3C3C3C"/>
																<stop offset="0.3125" style="stop-color:#222222"/>
																<stop offset="0.3757" style="stop-color:#0F0F0F"/>
																<stop offset="0.4408" style="stop-color:#040404"/>
																<stop offset="0.5108" style="stop-color:#000000"/>
																<stop offset="0.5523" style="stop-color:#090909"/>
																<stop offset="0.6209" style="stop-color:#222222"/>
																<stop offset="0.7081" style="stop-color:#4B4B4B"/>
																<stop offset="0.8105" style="stop-color:#848484"/>
																<stop offset="0.9244" style="stop-color:#CBCBCB"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															<path style="fill:url(#SVGID_00000163038844336021037350000012567685641694802068_);" d="M1524.117,933.407                 c-0.109-0.001-0.221-0.011-0.333-0.027l-123.301-18.368c-1.493-0.222-2.538-1.713-2.333-3.331                 c0.21-1.616,1.574-2.747,3.079-2.526l123.301,18.368c1.493,0.221,2.538,1.714,2.333,3.331                 C1526.671,932.351,1525.479,933.43,1524.117,933.407z"/>
														</g>
													</g>
													<path style="fill:#89909A;" d="M1530.184,930.818c0.286,6.524-4.366,12.063-10.391,12.373l-23.865,1.228               c-6.025,0.31-11.141-4.727-11.428-11.25l0,0c-0.286-6.524,4.365-12.063,10.391-12.373l23.865-1.228               C1524.782,919.257,1529.898,924.295,1530.184,930.818L1530.184,930.818z"/>
													<g style="opacity:0.22;">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000168085864302242920430000012920518374341949826_" filterUnits="userSpaceOnUse" x="1484.488" y="919.554" width="45.709" height="24.878">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1484.488" y="919.554" width="45.709" height="24.878" id="SVGID_00000073691237444940478110000015777025782554477486_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000168085864302242920430000012920518374341949826_);">
																<defs>
																	
																		<filter id="Adobe_OpacityMaskFilter_00000112631285244580514610000006494782019462075284_" filterUnits="userSpaceOnUse" x="1484.488" y="919.554" width="45.709" height="24.878">
																		<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																		<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																	</filter>
																</defs>
																
																	<mask maskUnits="userSpaceOnUse" x="1484.488" y="919.554" width="45.709" height="24.878" id="SVGID_00000073691237444940478110000015777025782554477486_">
																	<g style="filter:url(#Adobe_OpacityMaskFilter_00000112631285244580514610000006494782019462075284_);">
																	</g>
																</mask>
																
																	<linearGradient id="SVGID_00000023276764197709191690000015476557729475173261_" gradientUnits="userSpaceOnUse" x1="1462.3256" y1="969.1851" x2="1508.0116" y2="969.1851" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
																	<stop offset="0" style="stop-color:#FFFFFF"/>
																	<stop offset="0.028" style="stop-color:#EEEEEE"/>
																	<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																	<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																	<stop offset="0.2597" style="stop-color:#888888"/>
																	<stop offset="0.3397" style="stop-color:#757575"/>
																	<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																	<stop offset="0.5108" style="stop-color:#666666"/>
																	<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																	<stop offset="0.6716" style="stop-color:#888888"/>
																	<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																	<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																	<stop offset="1" style="stop-color:#FFFFFF"/>
																</linearGradient>
																
																	<path style="mask:url(#SVGID_00000073691237444940478110000015777025782554477486_);fill:url(#SVGID_00000023276764197709191690000015476557729475173261_);" d="                  M1530.184,930.818c0.286,6.524-4.366,12.063-10.391,12.373l-23.865,1.228c-6.025,0.31-11.141-4.727-11.428-11.25                  l0,0c-0.286-6.524,4.365-12.063,10.391-12.373l23.865-1.228C1524.782,919.257,1529.898,924.295,1530.184,930.818                  L1530.184,930.818z"/>
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000052787339349494204730000002756629902200472486_" gradientUnits="userSpaceOnUse" x1="1462.3256" y1="969.1851" x2="1508.0116" y2="969.1851" gradientTransform="matrix(0.9999 0.0158 -0.0158 0.9999 37.6357 -60.561)">
															<stop offset="0" style="stop-color:#FDFDFD"/>
															<stop offset="0.0102" style="stop-color:#F2F2F2"/>
															<stop offset="0.0685" style="stop-color:#BABABA"/>
															<stop offset="0.1286" style="stop-color:#898989"/>
															<stop offset="0.1892" style="stop-color:#5F5F5F"/>
															<stop offset="0.2504" style="stop-color:#3C3C3C"/>
															<stop offset="0.3125" style="stop-color:#222222"/>
															<stop offset="0.3757" style="stop-color:#0F0F0F"/>
															<stop offset="0.4408" style="stop-color:#040404"/>
															<stop offset="0.5108" style="stop-color:#000000"/>
															<stop offset="0.5523" style="stop-color:#090909"/>
															<stop offset="0.6209" style="stop-color:#222222"/>
															<stop offset="0.7081" style="stop-color:#4B4B4B"/>
															<stop offset="0.8105" style="stop-color:#848484"/>
															<stop offset="0.9244" style="stop-color:#CBCBCB"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														<path style="fill:url(#SVGID_00000052787339349494204730000002756629902200472486_);" d="M1530.184,930.818                c0.286,6.524-4.366,12.063-10.391,12.373l-23.865,1.228c-6.025,0.31-11.141-4.727-11.428-11.25l0,0                c-0.286-6.524,4.365-12.063,10.391-12.373l23.865-1.228C1524.782,919.257,1529.898,924.295,1530.184,930.818                L1530.184,930.818z"/>
													</g>
												</g>
											</g>
											<g>
												<path style="fill:#090C09;" d="M2109.994,948.468c-1.265-0.253-83.312-16.724-177.128-26.752              c-1.481-3.819-4.732-6.706-8.804-7.203l-23.741-2.905c-1.251-0.153-2.462-0.022-3.625,0.263l-80.406-26.62              c-1.454-0.479-2.967,0.403-3.411,1.962c-0.441,1.562,0.371,3.215,1.812,3.691l75.874,25.119              c-0.381,0.518-0.737,1.059-1.036,1.646c-36.803-2.899-73.795-4.337-107.065-3.031c-3.015,0.118-5.368,2.86-5.259,6.122              c0.095,2.835,2.018,5.131,4.497,5.607c0.375,0.073,0.762,0.103,1.158,0.087c33.208-1.304,70.193,0.16,106.979,3.084              c1.701,2.981,4.564,5.145,8.03,5.569l23.74,2.905c3.946,0.483,7.607-1.402,9.863-4.579              c93.37,9.96,175.247,26.396,176.512,26.658c0.016,0.003,0.034,0.007,0.05,0.009c2.948,0.567,5.77-1.542,6.323-4.733              C2114.911,952.158,2112.959,949.069,2109.994,948.468z"/>
												<g>
													<g>
														<path style="fill:#070709;" d="M1787.594,931.857c-2.478-0.476-4.401-2.772-4.497-5.607                c-0.109-3.263,2.245-6.005,5.259-6.122c133.036-5.223,325.586,33.441,327.529,33.83c2.965,0.601,4.917,3.69,4.363,6.9                c-0.553,3.191-3.375,5.3-6.323,4.733c-0.016-0.002-0.034-0.006-0.05-0.009c-1.938-0.4-193.3-38.813-325.123-33.637                C1788.358,931.96,1787.97,931.93,1787.594,931.857z"/>
													</g>
													<g>
														<path style="fill:#030303;" d="M1939.931,935.882c-0.108-0.02-0.216-0.049-0.325-0.084l-119.023-39.404                c-1.441-0.476-2.253-2.129-1.812-3.691c0.445-1.56,1.958-2.442,3.412-1.962l119.023,39.404                c1.441,0.476,2.254,2.129,1.813,3.691C1942.608,935.28,1941.273,936.14,1939.931,935.882z"/>
													</g>
													<g>
														<g style="opacity:0.22;">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000109010488232484587040000014155106582636806031_" filterUnits="userSpaceOnUse" x="1783.094" y="919.642" width="337.249" height="46.04">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="1783.094" y="919.642" width="337.249" height="46.04" id="SVGID_00000174599573203026283320000009478381064147411096_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000109010488232484587040000014155106582636806031_);">
																	<defs>
																		
																			<filter id="Adobe_OpacityMaskFilter_00000147202385289285360550000012788681042250921653_" filterUnits="userSpaceOnUse" x="1783.094" y="919.642" width="337.249" height="46.04">
																			<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																			<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																		</filter>
																	</defs>
																	
																		<mask maskUnits="userSpaceOnUse" x="1783.094" y="919.642" width="337.249" height="46.04" id="SVGID_00000174599573203026283320000009478381064147411096_">
																		<g style="filter:url(#Adobe_OpacityMaskFilter_00000147202385289285360550000012788681042250921653_);">
																		</g>
																	</mask>
																	
																		<linearGradient id="SVGID_00000038410378936391364390000006902651693988911521_" gradientUnits="userSpaceOnUse" x1="1567.3202" y1="1390.2561" x2="1905.4637" y2="1390.2561" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
																		<stop offset="0" style="stop-color:#FFFFFF"/>
																		<stop offset="0.028" style="stop-color:#EEEEEE"/>
																		<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																		<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																		<stop offset="0.2597" style="stop-color:#888888"/>
																		<stop offset="0.3397" style="stop-color:#757575"/>
																		<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																		<stop offset="0.5108" style="stop-color:#666666"/>
																		<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																		<stop offset="0.6716" style="stop-color:#888888"/>
																		<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																		<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																		<stop offset="1" style="stop-color:#FFFFFF"/>
																	</linearGradient>
																	
																		<path style="mask:url(#SVGID_00000174599573203026283320000009478381064147411096_);fill:url(#SVGID_00000038410378936391364390000006902651693988911521_);" d="                   M1787.594,931.857c-2.478-0.476-4.401-2.772-4.497-5.607c-0.109-3.263,2.245-6.005,5.259-6.122                   c133.036-5.223,325.586,33.441,327.529,33.83c2.965,0.601,4.917,3.69,4.363,6.9                   c-0.553,3.191-3.375,5.3-6.323,4.733c-0.016-0.002-0.034-0.006-0.05-0.009c-1.938-0.4-193.3-38.813-325.123-33.637                   C1788.358,931.96,1787.97,931.93,1787.594,931.857z"/>
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000069395787743481330620000018276613390357685129_" gradientUnits="userSpaceOnUse" x1="1567.3202" y1="1390.2561" x2="1905.4637" y2="1390.2561" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
																<stop offset="0" style="stop-color:#FDFDFD"/>
																<stop offset="0.0102" style="stop-color:#F2F2F2"/>
																<stop offset="0.0685" style="stop-color:#BABABA"/>
																<stop offset="0.1286" style="stop-color:#898989"/>
																<stop offset="0.1892" style="stop-color:#5F5F5F"/>
																<stop offset="0.2504" style="stop-color:#3C3C3C"/>
																<stop offset="0.3125" style="stop-color:#222222"/>
																<stop offset="0.3757" style="stop-color:#0F0F0F"/>
																<stop offset="0.4408" style="stop-color:#040404"/>
																<stop offset="0.5108" style="stop-color:#000000"/>
																<stop offset="0.5523" style="stop-color:#090909"/>
																<stop offset="0.6209" style="stop-color:#222222"/>
																<stop offset="0.7081" style="stop-color:#4B4B4B"/>
																<stop offset="0.8105" style="stop-color:#848484"/>
																<stop offset="0.9244" style="stop-color:#CBCBCB"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															<path style="fill:url(#SVGID_00000069395787743481330620000018276613390357685129_);" d="M1787.594,931.857                 c-2.478-0.476-4.401-2.772-4.497-5.607c-0.109-3.263,2.245-6.005,5.259-6.122                 c133.036-5.223,325.586,33.441,327.529,33.83c2.965,0.601,4.917,3.69,4.363,6.9c-0.553,3.191-3.375,5.3-6.323,4.733                 c-0.016-0.002-0.034-0.006-0.05-0.009c-1.938-0.4-193.3-38.813-325.123-33.637                 C1788.358,931.96,1787.97,931.93,1787.594,931.857z"/>
														</g>
													</g>
													<g>
														<g style="opacity:0.22;">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000113336149878257494630000018442052935572977558_" filterUnits="userSpaceOnUse" x="1818.651" y="890.609" width="124.487" height="45.318">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="1818.651" y="890.609" width="124.487" height="45.318" id="SVGID_00000007423608697143244280000005574326779439452293_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000113336149878257494630000018442052935572977558_);">
																	<defs>
																		
																			<filter id="Adobe_OpacityMaskFilter_00000003097820322611489680000011670979703685377961_" filterUnits="userSpaceOnUse" x="1818.651" y="890.609" width="124.487" height="45.318">
																			<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																			<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																		</filter>
																	</defs>
																	
																		<mask maskUnits="userSpaceOnUse" x="1818.651" y="890.609" width="124.487" height="45.318" id="SVGID_00000007423608697143244280000005574326779439452293_">
																		<g style="filter:url(#Adobe_OpacityMaskFilter_00000003097820322611489680000011670979703685377961_);">
																		</g>
																	</mask>
																	
																		<linearGradient id="SVGID_00000085948065123174518660000015923157587738703036_" gradientUnits="userSpaceOnUse" x1="1596.701" y1="1373.5992" x2="1726.2573" y2="1373.5992" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
																		<stop offset="0" style="stop-color:#FFFFFF"/>
																		<stop offset="0.028" style="stop-color:#EEEEEE"/>
																		<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																		<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																		<stop offset="0.2597" style="stop-color:#888888"/>
																		<stop offset="0.3397" style="stop-color:#757575"/>
																		<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																		<stop offset="0.5108" style="stop-color:#666666"/>
																		<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																		<stop offset="0.6716" style="stop-color:#888888"/>
																		<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																		<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																		<stop offset="1" style="stop-color:#FFFFFF"/>
																	</linearGradient>
																	
																		<path style="mask:url(#SVGID_00000007423608697143244280000005574326779439452293_);fill:url(#SVGID_00000085948065123174518660000015923157587738703036_);" d="                   M1939.931,935.882c-0.108-0.02-0.216-0.049-0.325-0.084l-119.023-39.404c-1.441-0.476-2.253-2.129-1.812-3.691                   c0.445-1.56,1.958-2.442,3.412-1.962l119.023,39.404c1.441,0.476,2.254,2.129,1.813,3.691                   C1942.608,935.28,1941.273,936.14,1939.931,935.882z"/>
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000054255840445884650300000000034784605111724983_" gradientUnits="userSpaceOnUse" x1="1596.701" y1="1373.5992" x2="1726.2573" y2="1373.5992" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
																<stop offset="0" style="stop-color:#FDFDFD"/>
																<stop offset="0.0102" style="stop-color:#F2F2F2"/>
																<stop offset="0.0685" style="stop-color:#BABABA"/>
																<stop offset="0.1286" style="stop-color:#898989"/>
																<stop offset="0.1892" style="stop-color:#5F5F5F"/>
																<stop offset="0.2504" style="stop-color:#3C3C3C"/>
																<stop offset="0.3125" style="stop-color:#222222"/>
																<stop offset="0.3757" style="stop-color:#0F0F0F"/>
																<stop offset="0.4408" style="stop-color:#040404"/>
																<stop offset="0.5108" style="stop-color:#000000"/>
																<stop offset="0.5523" style="stop-color:#090909"/>
																<stop offset="0.6209" style="stop-color:#222222"/>
																<stop offset="0.7081" style="stop-color:#4B4B4B"/>
																<stop offset="0.8105" style="stop-color:#848484"/>
																<stop offset="0.9244" style="stop-color:#CBCBCB"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															<path style="fill:url(#SVGID_00000054255840445884650300000000034784605111724983_);" d="M1939.931,935.882                 c-0.108-0.02-0.216-0.049-0.325-0.084l-119.023-39.404c-1.441-0.476-2.253-2.129-1.812-3.691                 c0.445-1.56,1.958-2.442,3.412-1.962l119.023,39.404c1.441,0.476,2.254,2.129,1.813,3.691                 C1942.608,935.28,1941.273,936.14,1939.931,935.882z"/>
														</g>
													</g>
													<path style="fill:#89909A;" d="M1946.302,934.372c-0.677,6.49-6.086,11.156-12.079,10.423l-23.741-2.905               c-5.994-0.733-10.304-6.588-9.626-13.078l0,0c0.677-6.489,6.086-11.155,12.079-10.422l23.74,2.905               C1942.67,922.028,1946.979,927.883,1946.302,934.372L1946.302,934.372z"/>
													<g style="opacity:0.22;">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000074434974895095036410000003172633176945517992_" filterUnits="userSpaceOnUse" x="1900.786" y="918.314" width="45.586" height="26.557">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1900.786" y="918.314" width="45.586" height="26.557" id="SVGID_00000000184866782954121590000010639213671756836534_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000074434974895095036410000003172633176945517992_);">
																<defs>
																	
																		<filter id="Adobe_OpacityMaskFilter_00000013882716266764819360000012734237420328564402_" filterUnits="userSpaceOnUse" x="1900.786" y="918.314" width="45.586" height="26.557">
																		<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																		<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																	</filter>
																</defs>
																
																	<mask maskUnits="userSpaceOnUse" x="1900.786" y="918.314" width="45.586" height="26.557" id="SVGID_00000000184866782954121590000010639213671756836534_">
																	<g style="filter:url(#Adobe_OpacityMaskFilter_00000013882716266764819360000012734237420328564402_);">
																	</g>
																</mask>
																
																	<linearGradient id="SVGID_00000054952366522802350360000010798733268968131970_" gradientUnits="userSpaceOnUse" x1="1683.8175" y1="1384.1747" x2="1729.6019" y2="1384.1747" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
																	<stop offset="0" style="stop-color:#FFFFFF"/>
																	<stop offset="0.028" style="stop-color:#EEEEEE"/>
																	<stop offset="0.1037" style="stop-color:#C4C4C4"/>
																	<stop offset="0.1811" style="stop-color:#A2A2A2"/>
																	<stop offset="0.2597" style="stop-color:#888888"/>
																	<stop offset="0.3397" style="stop-color:#757575"/>
																	<stop offset="0.4222" style="stop-color:#6A6A6A"/>
																	<stop offset="0.5108" style="stop-color:#666666"/>
																	<stop offset="0.5714" style="stop-color:#6F6F6F"/>
																	<stop offset="0.6716" style="stop-color:#888888"/>
																	<stop offset="0.7989" style="stop-color:#B1B1B1"/>
																	<stop offset="0.9468" style="stop-color:#E9E9E9"/>
																	<stop offset="1" style="stop-color:#FFFFFF"/>
																</linearGradient>
																
																	<path style="mask:url(#SVGID_00000000184866782954121590000010639213671756836534_);fill:url(#SVGID_00000054952366522802350360000010798733268968131970_);" d="                  M1946.302,934.372c-0.677,6.49-6.086,11.156-12.079,10.423l-23.741-2.905c-5.994-0.733-10.304-6.588-9.626-13.078                  l0,0c0.677-6.489,6.086-11.155,12.079-10.422l23.74,2.905C1942.67,922.028,1946.979,927.883,1946.302,934.372                  L1946.302,934.372z"/>
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000106129545525551560800000000919366026739251871_" gradientUnits="userSpaceOnUse" x1="1683.8175" y1="1384.1747" x2="1729.6019" y2="1384.1747" gradientTransform="matrix(0.9846 0.1749 -0.1749 0.9846 485.2451 -729.7698)">
															<stop offset="0" style="stop-color:#FDFDFD"/>
															<stop offset="0.0102" style="stop-color:#F2F2F2"/>
															<stop offset="0.0685" style="stop-color:#BABABA"/>
															<stop offset="0.1286" style="stop-color:#898989"/>
															<stop offset="0.1892" style="stop-color:#5F5F5F"/>
															<stop offset="0.2504" style="stop-color:#3C3C3C"/>
															<stop offset="0.3125" style="stop-color:#222222"/>
															<stop offset="0.3757" style="stop-color:#0F0F0F"/>
															<stop offset="0.4408" style="stop-color:#040404"/>
															<stop offset="0.5108" style="stop-color:#000000"/>
															<stop offset="0.5523" style="stop-color:#090909"/>
															<stop offset="0.6209" style="stop-color:#222222"/>
															<stop offset="0.7081" style="stop-color:#4B4B4B"/>
															<stop offset="0.8105" style="stop-color:#848484"/>
															<stop offset="0.9244" style="stop-color:#CBCBCB"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														<path style="fill:url(#SVGID_00000106129545525551560800000000919366026739251871_);" d="M1946.302,934.372                c-0.677,6.49-6.086,11.156-12.079,10.423l-23.741-2.905c-5.994-0.733-10.304-6.588-9.626-13.078l0,0                c0.677-6.489,6.086-11.155,12.079-10.422l23.74,2.905C1942.67,922.028,1946.979,927.883,1946.302,934.372                L1946.302,934.372z"/>
													</g>
												</g>
											</g>
										</g>
									</g>
								</g>
							</g>
							<g>
								<g>
									<path d="M1167.04,1684.548l-0.005,0.418c0.135,18.122-10.224,33.749-24.784,39.292c-3.083,1.219-6.436,1.944-9.923,2.113           l-18.757,0.894l-0.894-1.125c-10.141-20.609-16.135-44.027-16.894-68.428c-0.12-1.604-0.169-3.276-0.15-4.879           l-1.65-350.149l1.634-2.698c0,0,22.49-10.049,42.322,10.256c12.12,12.339,23.131,35.886,27.49,79.911l0.667,126.499           l0.564,99.734L1167.04,1684.548z"/>
									<path style="fill:#DADADA;" d="M1121.489,1711.049c-7.148-16.87-11.2-35.368-11.776-53.967l-0.042-0.808           c-0.083-1.067-0.113-2.183-0.101-3.249l-1.597-339.675c0.508-0.021,1.035-0.035,1.576-0.028           c22.013,0.26,36.898,27.881,41.947,77.795l1.607,293.58c0.079,14.09-9.302,25.269-21.352,25.857L1121.489,1711.049z"/>
									<g style="opacity:0.54;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000072963195771622948980000004444685875116640936_" filterUnits="userSpaceOnUse" x="1107.973" y="1313.319" width="45.131" height="397.73">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="1107.973" y="1313.319" width="45.131" height="397.73" id="SVGID_00000059988888946281195810000011018927961444994471_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000072963195771622948980000004444685875116640936_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000176758693141620559260000017754888605121048453_" filterUnits="userSpaceOnUse" x="1107.973" y="1313.319" width="45.131" height="397.73">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1107.973" y="1313.319" width="45.131" height="397.73" id="SVGID_00000059988888946281195810000011018927961444994471_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000176758693141620559260000017754888605121048453_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000047762818972345751610000015801193195535690890_" gradientUnits="userSpaceOnUse" x1="1118.1619" y1="1484.4677" x2="1167.6774" y2="1484.4677" gradientTransform="matrix(0.9999 0.0118 -0.0118 0.9999 5.0949 14.5546)">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.0575" style="stop-color:#F0F0F0"/>
													<stop offset="0.1676" style="stop-color:#CACACA"/>
													<stop offset="0.3174" style="stop-color:#8C8C8C"/>
													<stop offset="0.457" style="stop-color:#4D4D4D"/>
													<stop offset="0.517" style="stop-color:#565656"/>
													<stop offset="0.6162" style="stop-color:#6F6F6F"/>
													<stop offset="0.7423" style="stop-color:#989898"/>
													<stop offset="0.8887" style="stop-color:#D0D0D0"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000059988888946281195810000011018927961444994471_);fill:url(#SVGID_00000047762818972345751610000015801193195535690890_);" d="              M1121.489,1711.049c-7.148-16.87-11.2-35.368-11.776-53.967l-0.042-0.808c-0.083-1.067-0.113-2.183-0.101-3.249              l-1.597-339.675c0.508-0.021,1.035-0.035,1.576-0.028c22.013,0.26,36.898,27.881,41.947,77.795l1.607,293.58              c0.079,14.09-9.302,25.269-21.352,25.857L1121.489,1711.049z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000098929778946314270140000007258028489756768427_" gradientUnits="userSpaceOnUse" x1="1118.1619" y1="1484.4677" x2="1167.6774" y2="1484.4677" gradientTransform="matrix(0.9999 0.0118 -0.0118 0.9999 5.0949 14.5546)">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0475" style="stop-color:#EEF4F9"/>
											<stop offset="0.1384" style="stop-color:#C8DDEE"/>
											<stop offset="0.2634" style="stop-color:#89B7DC"/>
											<stop offset="0.4163" style="stop-color:#3483C3"/>
											<stop offset="0.457" style="stop-color:#1C75BC"/>
											<stop offset="0.5072" style="stop-color:#257BBF"/>
											<stop offset="0.5902" style="stop-color:#3E8AC6"/>
											<stop offset="0.6957" style="stop-color:#67A3D2"/>
											<stop offset="0.8195" style="stop-color:#A0C5E3"/>
											<stop offset="0.9573" style="stop-color:#E7F1F8"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000098929778946314270140000007258028489756768427_);" d="M1121.489,1711.049            c-7.148-16.87-11.2-35.368-11.776-53.967l-0.042-0.808c-0.083-1.067-0.113-2.183-0.101-3.249l-1.597-339.675            c0.508-0.021,1.035-0.035,1.576-0.028c22.013,0.26,36.898,27.881,41.947,77.795l1.607,293.58            c0.079,14.09-9.302,25.269-21.352,25.857L1121.489,1711.049z"/>
									</g>
									<g>
										<path style="fill:#000301;" d="M1156.917,1428.071c-21.966-15.504-58.099-40.819-61.543-42.729l5.101-14.72            c0.911,0.358,3.34,1.31,63.829,44.01L1156.917,1428.071z"/>
									</g>
								</g>
								<path style="opacity:0.24;fill:#FFFFFF;" d="M1152.596,1597.541l0.001-0.07c-7.656-12.564-14.375-27.837-19.816-45.115          c-10.62-33.229-16.619-73.999-16.101-117.899c0.134-11.358,0.684-22.502,1.649-33.362l0.001-0.069          c0.558-5.918,1.183-11.624,1.946-17.261c2.612-20.528,6.743-39.784,12.176-57.212c1.67-5.625,3.476-11.11,5.489-16.313          c12.12,12.339,23.131,35.886,27.49,79.911l0.667,126.499l0.564,99.734          C1161.707,1611.03,1156.972,1604.772,1152.596,1597.541z"/>
								<path style="opacity:0.24;fill:#FFFFFF;" d="M1167.04,1684.548l-0.005,0.418c0.135,18.122-10.224,33.749-24.784,39.292          c-1.477-4.896-2.812-9.929-4.074-15.102c-6.977-28.865-10.697-62.012-10.281-97.201c0.134-11.359,0.683-22.502,1.648-33.363          c0.559-5.917,1.183-11.693,1.947-17.33c0.384-2.992,0.766-5.985,1.29-8.905c2.573-17.183,6.25-33.448,10.884-48.307          c1.669-5.625,3.476-11.11,5.489-16.313c0.965,0.988,1.926,2.114,2.888,3.241c5.155,6.124,9.936,14.403,14.053,25.673          l0.564,99.734L1167.04,1684.548z"/>
							</g>
							<g>
								<g>
									<path d="M2256.499,1684.548l0.005,0.418c-0.134,18.122,10.225,33.749,24.785,39.292c3.082,1.219,6.436,1.944,9.922,2.113           l18.757,0.894l0.894-1.125c10.141-20.609,16.136-44.027,16.894-68.428c0.121-1.604,0.169-3.276,0.15-4.879l1.65-350.149           l-1.634-2.698c0,0-22.49-10.049-42.321,10.256c-12.12,12.339-23.132,35.886-27.49,79.911l-0.667,126.499l-0.564,99.734           L2256.499,1684.548z"/>
									<path style="fill:#DADADA;" d="M2302.05,1711.049c7.148-16.87,11.2-35.368,11.777-53.967l0.041-0.808           c0.083-1.067,0.114-2.183,0.102-3.249l1.597-339.675c-0.508-0.021-1.035-0.035-1.576-0.028           c-22.014,0.26-36.899,27.881-41.948,77.795l-1.607,293.58c-0.078,14.09,9.302,25.269,21.352,25.857L2302.05,1711.049z"/>
									<g style="opacity:0.54;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000098208132317069170280000012502365085140647065_" filterUnits="userSpaceOnUse" x="2270.435" y="1313.319" width="45.132" height="397.73">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2270.435" y="1313.319" width="45.132" height="397.73" id="SVGID_00000125594768277769999410000000336852690620986759_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000098208132317069170280000012502365085140647065_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000149360217434219384280000008977301314441263498_" filterUnits="userSpaceOnUse" x="2270.435" y="1313.319" width="45.132" height="397.73">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2270.435" y="1313.319" width="45.132" height="397.73" id="SVGID_00000125594768277769999410000000336852690620986759_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000149360217434219384280000008977301314441263498_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000131341323602888952810000013932868959790355118_" gradientUnits="userSpaceOnUse" x1="-4813.7319" y1="1554.4709" x2="-4764.2153" y2="1554.4709" gradientTransform="matrix(-0.9999 0.0118 0.0118 0.9999 -2513.6816 14.5546)">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.0575" style="stop-color:#F0F0F0"/>
													<stop offset="0.1676" style="stop-color:#CACACA"/>
													<stop offset="0.3174" style="stop-color:#8C8C8C"/>
													<stop offset="0.457" style="stop-color:#4D4D4D"/>
													<stop offset="0.517" style="stop-color:#565656"/>
													<stop offset="0.6162" style="stop-color:#6F6F6F"/>
													<stop offset="0.7423" style="stop-color:#989898"/>
													<stop offset="0.8887" style="stop-color:#D0D0D0"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<path style="mask:url(#SVGID_00000125594768277769999410000000336852690620986759_);fill:url(#SVGID_00000131341323602888952810000013932868959790355118_);" d="              M2302.05,1711.049c7.148-16.87,11.2-35.368,11.777-53.967l0.041-0.808c0.083-1.067,0.114-2.183,0.102-3.249              l1.597-339.675c-0.508-0.021-1.035-0.035-1.576-0.028c-22.014,0.26-36.899,27.881-41.948,77.795l-1.607,293.58              c-0.078,14.09,9.302,25.269,21.352,25.857L2302.05,1711.049z"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000101093856029749034790000003811892802583578001_" gradientUnits="userSpaceOnUse" x1="-4813.7319" y1="1554.4709" x2="-4764.2153" y2="1554.4709" gradientTransform="matrix(-0.9999 0.0118 0.0118 0.9999 -2513.6816 14.5546)">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0475" style="stop-color:#EEF4F9"/>
											<stop offset="0.1384" style="stop-color:#C8DDEE"/>
											<stop offset="0.2634" style="stop-color:#89B7DC"/>
											<stop offset="0.4163" style="stop-color:#3483C3"/>
											<stop offset="0.457" style="stop-color:#1C75BC"/>
											<stop offset="0.5072" style="stop-color:#257BBF"/>
											<stop offset="0.5902" style="stop-color:#3E8AC6"/>
											<stop offset="0.6957" style="stop-color:#67A3D2"/>
											<stop offset="0.8195" style="stop-color:#A0C5E3"/>
											<stop offset="0.9573" style="stop-color:#E7F1F8"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<path style="fill:url(#SVGID_00000101093856029749034790000003811892802583578001_);" d="M2302.05,1711.049            c7.148-16.87,11.2-35.368,11.777-53.967l0.041-0.808c0.083-1.067,0.114-2.183,0.102-3.249l1.597-339.675            c-0.508-0.021-1.035-0.035-1.576-0.028c-22.014,0.26-36.899,27.881-41.948,77.795l-1.607,293.58            c-0.078,14.09,9.302,25.269,21.352,25.857L2302.05,1711.049z"/>
									</g>
									<g>
										<path style="fill:#000301;" d="M2266.622,1428.071c21.967-15.504,58.099-40.819,61.544-42.729l-5.102-14.72            c-0.911,0.358-3.34,1.31-63.829,44.01L2266.622,1428.071z"/>
									</g>
								</g>
								<path style="opacity:0.24;fill:#FFFFFF;" d="M2270.943,1597.541l-0.001-0.07c7.656-12.564,14.375-27.837,19.817-45.115          c10.619-33.229,16.618-73.999,16.1-117.899c-0.134-11.358-0.685-22.502-1.648-33.362l-0.001-0.069          c-0.558-5.918-1.184-11.624-1.946-17.261c-2.614-20.528-6.744-39.784-12.177-57.212c-1.669-5.625-3.476-11.11-5.489-16.313          c-12.12,12.339-23.132,35.886-27.49,79.911l-0.667,126.499l-0.564,99.734          C2261.832,1611.03,2266.567,1604.772,2270.943,1597.541z"/>
								<path style="opacity:0.24;fill:#FFFFFF;" d="M2256.499,1684.548l0.005,0.418c-0.134,18.122,10.225,33.749,24.785,39.292          c1.477-4.896,2.811-9.929,4.074-15.102c6.977-28.865,10.697-62.012,10.28-97.201c-0.134-11.359-0.682-22.502-1.647-33.363          c-0.558-5.917-1.183-11.693-1.947-17.33c-0.385-2.992-0.766-5.985-1.29-8.905c-2.573-17.183-6.25-33.448-10.884-48.307          c-1.67-5.625-3.477-11.11-5.49-16.313c-0.965,0.988-1.926,2.114-2.889,3.241c-5.154,6.124-9.936,14.403-14.054,25.673          l-0.564,99.734L2256.499,1684.548z"/>
							</g>
							<g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1300.546,1399.347c0,0,0.415,5.537,1.14,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.033,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.454,40.222,0.677,56.865c0.199,16.644-0.07,30.526-0.054,40.238c-0.134,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.144-15.227c-0.577-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.048-36.012-2.798-56.801           c-0.781-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.193-16.644,0.065-30.526,0.049-40.238           C1300.47,1404.899,1300.546,1399.347,1300.546,1399.347z"/>
									<path style="fill:#000301;" d="M1335.187,1399.347c0,0-0.415,5.537-1.141,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.677,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.415-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.512-40.159c0.793-16.625,2.047-36.012,2.797-56.801c0.782-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.193-16.644-0.064-30.526-0.049-40.238           C1335.263,1404.899,1335.187,1399.347,1335.187,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1379.327,1399.347c0,0,0.415,5.537,1.141,15.228c0.577,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.801,56.801c0.782,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.453,40.222,0.676,56.865c0.199,16.644-0.069,30.526-0.053,40.238c-0.135,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.693-23.534-2.512-40.159c-0.794-16.625-2.047-36.012-2.797-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.504-20.797-0.453-40.223-0.675-56.865c-0.192-16.644,0.065-30.526,0.05-40.238           C1379.251,1404.899,1379.327,1399.347,1379.327,1399.347z"/>
									<path style="fill:#000301;" d="M1413.968,1399.347c0,0-0.415,5.537-1.141,15.228c-0.578,9.694-1.682,23.535-2.505,40.159           c-0.797,16.625-2.033,36.012-2.802,56.801c-0.783,20.789-1.618,42.962-2.455,65.136           c-0.521,22.183-1.044,44.367-1.532,65.164c-0.521,20.796-0.453,40.222-0.677,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.211,15.269,0.211,15.269s0.416-5.537,1.144-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.047-36.012,2.798-56.801c0.781-20.788,1.615-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.529-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.049-40.238           C1414.044,1404.899,1413.968,1399.347,1413.968,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1458.107,1399.347c0,0,0.415,5.537,1.141,15.228c0.578,9.694,1.682,23.535,2.505,40.159           c0.797,16.625,2.032,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.455,65.136c0.521,22.183,1.043,44.367,1.532,65.164           c0.521,20.796,0.454,40.222,0.677,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.134,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.047-36.012-2.798-56.801           c-0.781-20.788-1.615-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.529-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.049-40.238           C1458.031,1404.899,1458.107,1399.347,1458.107,1399.347z"/>
									<path style="fill:#000301;" d="M1492.749,1399.347c0,0-0.414,5.537-1.14,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.782,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.053,40.238c0.135,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.144-15.227           c0.578-9.694,1.692-23.534,2.511-40.159c0.794-16.625,2.047-36.012,2.797-56.801c0.782-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.05-40.238           C1492.824,1404.899,1492.749,1399.347,1492.749,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1536.888,1399.347c0,0,0.415,5.537,1.14,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.043,44.367,1.533,65.164           c0.521,20.796,0.453,40.222,0.677,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.134,9.717-0.211,15.269-0.211,15.269           s-0.416-5.537-1.144-15.227c-0.578-9.694-1.693-23.534-2.512-40.159c-0.793-16.625-2.047-36.012-2.797-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.193-16.644,0.064-30.526,0.049-40.238           C1536.813,1404.899,1536.888,1399.347,1536.888,1399.347z"/>
									<path style="fill:#000301;" d="M1571.53,1399.347c0,0-0.415,5.537-1.14,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.677,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.144-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.048-36.012,2.798-56.801c0.781-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.193-16.644-0.065-30.526-0.049-40.238           C1571.605,1404.899,1571.53,1399.347,1571.53,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1615.669,1399.347c0,0,0.415,5.537,1.141,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.801,56.801c0.782,20.789,1.617,42.962,2.454,65.136c0.521,22.183,1.043,44.367,1.533,65.164           c0.521,20.796,0.454,40.222,0.676,56.865c0.199,16.644-0.069,30.526-0.053,40.238c-0.135,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.692-23.534-2.511-40.159c-0.794-16.625-2.047-36.012-2.797-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.529-65.164           c-0.503-20.797-0.452-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.049-40.238           C1615.593,1404.899,1615.669,1399.347,1615.669,1399.347z"/>
									<path style="fill:#000301;" d="M1650.311,1399.347c0,0-0.415,5.537-1.141,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.455,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.454,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.047-36.012,2.798-56.801c0.781-20.788,1.615-42.962,2.448-65.135           c0.521-22.183,1.041-44.368,1.529-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.049-40.238           C1650.387,1404.899,1650.311,1399.347,1650.311,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1694.45,1399.347c0,0,0.415,5.537,1.141,15.228c0.578,9.694,1.682,23.535,2.506,40.159           c0.796,16.625,2.033,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.454,65.136c0.522,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.453,40.222,0.677,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.134,9.717-0.211,15.269-0.211,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.048-36.012-2.798-56.801           c-0.781-20.788-1.615-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.529-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.049-40.238           C1694.374,1404.899,1694.45,1399.347,1694.45,1399.347z"/>
									<path style="fill:#000301;" d="M1729.091,1399.347c0,0-0.415,5.537-1.14,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.454,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.053,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.512-40.159c0.793-16.625,2.047-36.012,2.797-56.801c0.782-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.193-16.644-0.064-30.526-0.049-40.238           C1729.167,1404.899,1729.091,1399.347,1729.091,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1773.231,1399.347c0,0,0.415,5.537,1.14,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.033,36.012,2.802,56.801c0.782,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.043,44.367,1.533,65.164           c0.521,20.796,0.454,40.222,0.676,56.865c0.199,16.644-0.069,30.526-0.053,40.238c-0.134,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.693-23.534-2.512-40.159c-0.793-16.625-2.047-36.012-2.797-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.05-40.238           C1773.155,1404.899,1773.231,1399.347,1773.231,1399.347z"/>
									<path style="fill:#000301;" d="M1807.873,1399.347c0,0-0.415,5.537-1.141,15.228c-0.578,9.694-1.682,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.454,65.136           c-0.522,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.677,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.048-36.012,2.798-56.801c0.781-20.788,1.615-42.962,2.448-65.135           c0.521-22.183,1.041-44.368,1.529-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.049-40.238           C1807.948,1404.899,1807.873,1399.347,1807.873,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1852.011,1399.347c0,0,0.415,5.537,1.141,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.8,56.801c0.783,20.789,1.618,42.962,2.455,65.136c0.521,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.453,40.222,0.676,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.134,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.047-36.012-2.798-56.801           c-0.781-20.788-1.615-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.529-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.049-40.238           C1851.936,1404.899,1852.011,1399.347,1852.011,1399.347z"/>
									<path style="fill:#000301;" d="M1886.654,1399.347c0,0-0.415,5.537-1.141,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.782,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.043,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.053,40.238c0.135,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.144-15.227           c0.577-9.694,1.692-23.534,2.511-40.159c0.794-16.625,2.047-36.012,2.797-56.801c0.782-20.788,1.615-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.529-65.164c0.503-20.797,0.452-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.049-40.238           C1886.729,1404.899,1886.654,1399.347,1886.654,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M1930.793,1399.347c0,0,0.415,5.537,1.14,15.228c0.578,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.454,40.222,0.677,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.134,9.717-0.211,15.269-0.211,15.269           s-0.416-5.537-1.144-15.227c-0.577-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.048-36.012-2.798-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.193-16.644,0.065-30.526,0.049-40.238           C1930.717,1404.899,1930.793,1399.347,1930.793,1399.347z"/>
									<path style="fill:#000301;" d="M1965.434,1399.347c0,0-0.415,5.537-1.14,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.453,40.222-0.677,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.211,15.269,0.211,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.048-36.012,2.798-56.801c0.782-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.193-16.644-0.064-30.526-0.049-40.238           C1965.51,1404.899,1965.434,1399.347,1965.434,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M2009.574,1399.347c0,0,0.415,5.537,1.141,15.228c0.577,9.694,1.683,23.535,2.506,40.159           c0.796,16.625,2.032,36.012,2.801,56.801c0.782,20.789,1.618,42.962,2.454,65.136c0.521,22.183,1.044,44.367,1.533,65.164           c0.521,20.796,0.453,40.222,0.676,56.865c0.199,16.644-0.069,30.526-0.053,40.238c-0.135,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.143-15.227c-0.578-9.694-1.692-23.534-2.511-40.159c-0.794-16.625-2.047-36.012-2.797-56.801           c-0.782-20.788-1.616-42.962-2.449-65.135c-0.521-22.183-1.041-44.368-1.528-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.05-40.238           C2009.498,1404.899,2009.574,1399.347,2009.574,1399.347z"/>
									<path style="fill:#000301;" d="M2044.215,1399.347c0,0-0.415,5.537-1.141,15.228c-0.577,9.694-1.682,23.535-2.505,40.159           c-0.797,16.625-2.032,36.012-2.801,56.801c-0.783,20.789-1.618,42.962-2.455,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.52,20.796-0.453,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.054,40.238c0.134,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.511-40.159c0.794-16.625,2.047-36.012,2.798-56.801c0.781-20.788,1.615-42.962,2.448-65.135           c0.521-22.183,1.041-44.368,1.529-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.049-40.238           C2044.291,1404.899,2044.215,1399.347,2044.215,1399.347z"/>
								</g>
								<g style="opacity:0.3;">
									<path style="fill:#FFFFFF;" d="M2088.354,1399.347c0,0,0.415,5.537,1.141,15.228c0.578,9.694,1.682,23.535,2.506,40.159           c0.797,16.625,2.032,36.012,2.801,56.801c0.783,20.789,1.618,42.962,2.455,65.136c0.521,22.183,1.043,44.367,1.532,65.164           c0.521,20.796,0.453,40.222,0.677,56.865c0.199,16.644-0.069,30.526-0.054,40.238c-0.135,9.717-0.212,15.269-0.212,15.269           s-0.416-5.537-1.144-15.227c-0.577-9.694-1.693-23.534-2.511-40.159c-0.794-16.625-2.047-36.012-2.798-56.801           c-0.781-20.788-1.615-42.962-2.448-65.135c-0.521-22.183-1.041-44.368-1.529-65.164           c-0.503-20.797-0.453-40.223-0.674-56.865c-0.192-16.644,0.065-30.526,0.049-40.238           C2088.278,1404.899,2088.354,1399.347,2088.354,1399.347z"/>
									<path style="fill:#000301;" d="M2122.996,1399.347c0,0-0.414,5.537-1.14,15.228c-0.578,9.694-1.683,23.535-2.506,40.159           c-0.796,16.625-2.032,36.012-2.801,56.801c-0.782,20.789-1.618,42.962-2.454,65.136           c-0.521,22.183-1.044,44.367-1.533,65.164c-0.521,20.796-0.454,40.222-0.676,56.865           c-0.199,16.644,0.069,30.526,0.053,40.238c0.135,9.717,0.212,15.269,0.212,15.269s0.416-5.537,1.143-15.227           c0.578-9.694,1.693-23.534,2.512-40.159c0.794-16.625,2.047-36.012,2.798-56.801c0.782-20.788,1.616-42.962,2.449-65.135           c0.521-22.183,1.041-44.368,1.528-65.164c0.503-20.797,0.453-40.223,0.674-56.865c0.192-16.644-0.065-30.526-0.05-40.238           C2123.071,1404.899,2122.996,1399.347,2122.996,1399.347z"/>
								</g>
							</g>
						</g>
						<g>
							<g>
								<g>
									<path style="fill:#1C1C1C;" d="M1400.416,592.271c-0.079,0.418-0.158,0.864-0.236,1.309           c-1.257,6.674-3.953,15.259-8.846,26.252c-2.094,4.737-4.607,9.92-7.616,15.599c-2.54,4.79-5.392,9.946-8.664,15.469           c-8.69,14.815-20.102,32.298-34.994,52.976c-3.245,4.58-6.727,9.317-10.365,14.212c-1.727,2.33-3.455,4.607-5.208,6.858           c-74.7,97.052-143.589,110.532-152.698,59.388c-3.089-17.301,0.681-42.009,13.375-73.392           c3.821-9.423,8.245-18.243,13.165-26.462c0.471-0.785,0.942-1.57,1.466-2.355c4.764-7.773,9.972-14.997,15.521-21.724           c16.228-19.735,35.413-35.204,55.07-47.348c27.874-17.222,56.744-27.692,79.594-33.973           c12.04-3.324,22.431-5.471,30.126-6.805C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
									<g>
										<g style="opacity:0.36;">
											<polygon style="fill:#D6EADC;" points="1245.381,656.131 1243.877,653.855 1369.708,599.023 1371.212,601.298                         "/>
											<path style="fill:#D6EADC;" d="M1378.892,612.929l-1.911,2.984l-7.119-10.732l-1.676-2.538l-0.654-0.969             c0-0.027,0-0.027,0-0.027l-0.863-1.309l3.036-1.308l0.367,0.55l0.104,0.157l1.047,1.57L1378.892,612.929z"/>
											<g>
												<path style="fill:#FFFFFF;" d="M1373.169,620.958l-8.167,3.559h-0.026l-1.806,0.812l-8.716,3.795l-0.288,0.105              l-3.743,1.648l-0.942-1.413l-9.579-14.501v-0.027l-1.518-2.251v-0.026l-3.036,1.335l1.518,2.277l10.522,15.913              l-4.685,2.042l-23.609,10.287l-10.522-15.914l-0.759-1.126l-0.759-1.151l-1.282,0.576h-0.026l-1.728,0.733v0.026              l1.519,2.278l10.522,15.914l-28.294,12.328l-10.522-15.913l-1.518-2.278l-3.036,1.309l1.518,2.277l10.522,15.94              l-19.63,8.558l-0.917,0.393l-7.747,3.377l-3.429-5.183l-7.093-10.757l-1.152-1.702l-0.367-0.549v-0.026l-0.104,0.052              l-0.105,0.052l-0.288,0.131h-0.026l-2.513,1.099l3.272,4.947v0.027l0.34,0.497l3.48,5.261l10.365,15.679l5.575,8.427              l1.937,2.958l1.858,2.826l1.623,2.46l4.057,6.125l1.1,1.649l3.114,4.71l1.649,2.488l10.313,15.625l0.942,1.414              l2.539-1.1l10.417-4.528l3.376-4.423l-14.291,6.229l-0.497,0.21l-9.894-14.946l-0.628-0.969l4.135-1.805l7.068-3.089              l3.821-1.649l4.738-2.067l8.533-3.718l4.266,6.414l0.812,1.256l1.257,1.911l2.094-2.748l-0.471-0.681l-0.55-0.838              l-4.371-6.648l10.706-4.66l6.282-2.747l0.444-0.183l3.324-4.398l-2.826,1.23l-8.061,3.508l-11.386,4.973l-0.654-0.995              l-3.062-4.632l-0.602-0.891l-4.529-6.858l-0.497-0.759l-1.178-1.779l12.276-5.365h0.026l1.099-0.472l0.759-0.34              l5.706-2.487l4.895-2.146l3.533-1.545l1.36,2.068l0.497,0.76l1.388,2.067l2.59,3.978l2.854,4.293              c0.706-0.941,1.413-1.858,2.068-2.774l-5.182-7.852l-0.027-0.026l-0.943-1.413l-0.314-0.498l-1.257-1.911l5.287-2.303              l4.214-1.831h0.026l7.904-3.43c0,0,0.052-0.026,0.052-0.052l4.213-1.833c0.76-1.308,1.518-2.617,2.252-3.926              l-3.769,1.649h-0.026l-7.04,3.089l-0.052,0.027l-3.063,1.335l-1.884,0.812l-9.632,4.188l-0.603-0.916l-1.675-2.538              c0,0,0,0,0-0.027l-4.083-6.151l-4.162-6.307l24.63-10.731l2.224-0.968l0.68-0.288l0.76-0.34l0.261,0.418l5.025,7.591              l0.733,1.1l0.446,0.654l1.674,2.539l0.289,0.418l0.941,1.414c0.576-1.099,1.179-2.172,1.728-3.272l-0.052-0.079              l-0.995-1.466l-1.308-1.988l-0.785-1.204l-0.55-0.811l-3.01-4.581l-1.361-2.041l4.817-2.094l6.307-2.775h0.026              l0.288-0.13l7.93-3.455l2.224-3.429l0.419-0.654L1373.169,620.958z M1262.506,681.053l-4.58-6.936l10.417-4.528              l3.036-1.336l14.841-6.464l3.193,4.816l3.559,5.365l0.786,1.204l2.25,3.377l0.76,1.152l-2.46,1.073l-25.86,11.255              L1262.506,681.053z M1300.091,682.754l4.738,7.172l0.524,0.812l2.932,4.423l0.497,0.759l-8.271,3.612l-10.521,4.581              l-9.501,4.135l-1.911-2.879l-1.151-1.754l-4.162-6.307l-0.471-0.707l-1.308-1.989l-1.518-2.304l5.365-2.329              c0,0,0.027,0,0.052-0.026l19.369-8.455l3.507-1.518l1.335,2.016L1300.091,682.754z M1321.947,654.748l4.607,6.989              l1.02,1.544l0.524,0.785l-7.015,3.063c0,0,0,0-0.026,0l-8.036,3.507l-10.993,4.789l-2.251,0.97l-0.314-0.471              l-2.748-4.162l-3.089-4.685l-0.76-1.178l-3.611-5.445l28.293-12.328L1321.947,654.748z"/>
											</g>
											<g style="opacity:0.54;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000183242771409205090480000018410030598002607528_" filterUnits="userSpaceOnUse" x="1241.358" y="612.661" width="132.57" height="117.389">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1241.358" y="612.661" width="132.57" height="117.389" id="SVGID_00000150065851450585582500000001998824866320746672_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000183242771409205090480000018410030598002607528_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000081644918728941632270000006092927780221322401_" filterUnits="userSpaceOnUse" x="1241.358" y="612.661" width="132.57" height="117.389">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1241.358" y="612.661" width="132.57" height="117.389" id="SVGID_00000150065851450585582500000001998824866320746672_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000081644918728941632270000006092927780221322401_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000011749063346388758450000007714637592159926918_" gradientUnits="userSpaceOnUse" x1="-1301.4197" y1="3938.4678" x2="-1210.6924" y2="3938.4678" gradientTransform="matrix(0.4699 0.8827 -0.8827 0.4699 5388.1509 -78.2519)">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0575" style="stop-color:#F0F0F0"/>
															<stop offset="0.1676" style="stop-color:#CACACA"/>
															<stop offset="0.3174" style="stop-color:#8C8C8C"/>
															<stop offset="0.457" style="stop-color:#4D4D4D"/>
															<stop offset="0.517" style="stop-color:#565656"/>
															<stop offset="0.6162" style="stop-color:#6F6F6F"/>
															<stop offset="0.7423" style="stop-color:#989898"/>
															<stop offset="0.8887" style="stop-color:#D0D0D0"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000150065851450585582500000001998824866320746672_);fill:url(#SVGID_00000011749063346388758450000007714637592159926918_);" d="                M1373.169,620.958l-8.167,3.559h-0.026l-1.806,0.812l-8.716,3.795l-0.288,0.105l-3.743,1.648l-0.942-1.413                l-9.579-14.501v-0.027l-1.518-2.251v-0.026l-3.036,1.335l1.518,2.277l10.522,15.913l-4.685,2.042l-23.609,10.287                l-10.522-15.914l-0.759-1.126l-0.759-1.151l-1.282,0.576h-0.026l-1.728,0.733v0.026l1.519,2.278l10.522,15.914                l-28.294,12.328l-10.522-15.913l-1.518-2.278l-3.036,1.309l1.518,2.277l10.522,15.94l-19.63,8.558l-0.917,0.393                l-7.747,3.377l-3.429-5.183l-7.093-10.757l-1.152-1.702l-0.367-0.549v-0.026l-0.104,0.052l-0.105,0.052l-0.288,0.131                h-0.026l-2.513,1.099l3.272,4.947v0.027l0.34,0.497l3.48,5.261l10.365,15.679l5.575,8.427l1.937,2.958l1.858,2.826                l1.623,2.46l4.057,6.125l1.1,1.649l3.114,4.71l1.649,2.488l10.313,15.625l0.942,1.414l2.539-1.1l10.417-4.528                l3.376-4.423l-14.291,6.229l-0.497,0.21l-9.894-14.946l-0.628-0.969l4.135-1.805l7.068-3.089l3.821-1.649l4.738-2.067                l8.533-3.718l4.266,6.414l0.812,1.256l1.257,1.911l2.094-2.748l-0.471-0.681l-0.55-0.838l-4.371-6.648l10.706-4.66                l6.282-2.747l0.444-0.183l3.324-4.398l-2.826,1.23l-8.061,3.508l-11.386,4.973l-0.654-0.995l-3.062-4.632                l-0.602-0.891l-4.529-6.858l-0.497-0.759l-1.178-1.779l12.276-5.365h0.026l1.099-0.472l0.759-0.34l5.706-2.487                l4.895-2.146l3.533-1.545l1.36,2.068l0.497,0.76l1.388,2.067l2.59,3.978l2.854,4.293                c0.706-0.941,1.413-1.858,2.068-2.774l-5.182-7.852l-0.027-0.026l-0.943-1.413l-0.314-0.498l-1.257-1.911l5.287-2.303                l4.214-1.831h0.026l7.904-3.43c0,0,0.052-0.026,0.052-0.052l4.213-1.833c0.76-1.308,1.518-2.617,2.252-3.926                l-3.769,1.649h-0.026l-7.04,3.089l-0.052,0.027l-3.063,1.335l-1.884,0.812l-9.632,4.188l-0.603-0.916l-1.675-2.538                c0,0,0,0,0-0.027l-4.083-6.151l-4.162-6.307l24.63-10.731l2.224-0.968l0.68-0.288l0.76-0.34l0.261,0.418l5.025,7.591                l0.733,1.1l0.446,0.654l1.674,2.539l0.289,0.418l0.941,1.414c0.576-1.099,1.179-2.172,1.728-3.272l-0.052-0.079                l-0.995-1.466l-1.308-1.988l-0.785-1.204l-0.55-0.811l-3.01-4.581l-1.361-2.041l4.817-2.094l6.307-2.775h0.026                l0.288-0.13l7.93-3.455l2.224-3.429l0.419-0.654L1373.169,620.958z M1262.506,681.053l-4.58-6.936l10.417-4.528                l3.036-1.336l14.841-6.464l3.193,4.816l3.559,5.365l0.786,1.204l2.25,3.377l0.76,1.152l-2.46,1.073l-25.86,11.255                L1262.506,681.053z M1300.091,682.754l4.738,7.172l0.524,0.812l2.932,4.423l0.497,0.759l-8.271,3.612l-10.521,4.581                l-9.501,4.135l-1.911-2.879l-1.151-1.754l-4.162-6.307l-0.471-0.707l-1.308-1.989l-1.518-2.304l5.365-2.329                c0,0,0.027,0,0.052-0.026l19.369-8.455l3.507-1.518l1.335,2.016L1300.091,682.754z M1321.947,654.748l4.607,6.989                l1.02,1.544l0.524,0.785l-7.015,3.063c0,0,0,0-0.026,0l-8.036,3.507l-10.993,4.789l-2.251,0.97l-0.314-0.471                l-2.748-4.162l-3.089-4.685l-0.76-1.178l-3.611-5.445l28.293-12.328L1321.947,654.748z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000031203719652932623040000010875484862562877571_" gradientUnits="userSpaceOnUse" x1="-1301.4197" y1="3938.4678" x2="-1210.6924" y2="3938.4678" gradientTransform="matrix(0.4699 0.8827 -0.8827 0.4699 5388.1509 -78.2519)">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0475" style="stop-color:#EEF4F9"/>
													<stop offset="0.1384" style="stop-color:#C8DDEE"/>
													<stop offset="0.2634" style="stop-color:#89B7DC"/>
													<stop offset="0.4163" style="stop-color:#3483C3"/>
													<stop offset="0.457" style="stop-color:#1C75BC"/>
													<stop offset="0.5072" style="stop-color:#257BBF"/>
													<stop offset="0.5902" style="stop-color:#3E8AC6"/>
													<stop offset="0.6957" style="stop-color:#67A3D2"/>
													<stop offset="0.8195" style="stop-color:#A0C5E3"/>
													<stop offset="0.9573" style="stop-color:#E7F1F8"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000031203719652932623040000010875484862562877571_);" d="M1373.169,620.958              l-8.167,3.559h-0.026l-1.806,0.812l-8.716,3.795l-0.288,0.105l-3.743,1.648l-0.942-1.413l-9.579-14.501v-0.027              l-1.518-2.251v-0.026l-3.036,1.335l1.518,2.277l10.522,15.913l-4.685,2.042l-23.609,10.287l-10.522-15.914l-0.759-1.126              l-0.759-1.151l-1.282,0.576h-0.026l-1.728,0.733v0.026l1.519,2.278l10.522,15.914l-28.294,12.328l-10.522-15.913              l-1.518-2.278l-3.036,1.309l1.518,2.277l10.522,15.94l-19.63,8.558l-0.917,0.393l-7.747,3.377l-3.429-5.183              l-7.093-10.757l-1.152-1.702l-0.367-0.549v-0.026l-0.104,0.052l-0.105,0.052l-0.288,0.131h-0.026l-2.513,1.099              l3.272,4.947v0.027l0.34,0.497l3.48,5.261l10.365,15.679l5.575,8.427l1.937,2.958l1.858,2.826l1.623,2.46l4.057,6.125              l1.1,1.649l3.114,4.71l1.649,2.488l10.313,15.625l0.942,1.414l2.539-1.1l10.417-4.528l3.376-4.423l-14.291,6.229              l-0.497,0.21l-9.894-14.946l-0.628-0.969l4.135-1.805l7.068-3.089l3.821-1.649l4.738-2.067l8.533-3.718l4.266,6.414              l0.812,1.256l1.257,1.911l2.094-2.748l-0.471-0.681l-0.55-0.838l-4.371-6.648l10.706-4.66l6.282-2.747l0.444-0.183              l3.324-4.398l-2.826,1.23l-8.061,3.508l-11.386,4.973l-0.654-0.995l-3.062-4.632l-0.602-0.891l-4.529-6.858              l-0.497-0.759l-1.178-1.779l12.276-5.365h0.026l1.099-0.472l0.759-0.34l5.706-2.487l4.895-2.146l3.533-1.545l1.36,2.068              l0.497,0.76l1.388,2.067l2.59,3.978l2.854,4.293c0.706-0.941,1.413-1.858,2.068-2.774l-5.182-7.852l-0.027-0.026              l-0.943-1.413l-0.314-0.498l-1.257-1.911l5.287-2.303l4.214-1.831h0.026l7.904-3.43c0,0,0.052-0.026,0.052-0.052              l4.213-1.833c0.76-1.308,1.518-2.617,2.252-3.926l-3.769,1.649h-0.026l-7.04,3.089l-0.052,0.027l-3.063,1.335              l-1.884,0.812l-9.632,4.188l-0.603-0.916l-1.675-2.538c0,0,0,0,0-0.027l-4.083-6.151l-4.162-6.307l24.63-10.731              l2.224-0.968l0.68-0.288l0.76-0.34l0.261,0.418l5.025,7.591l0.733,1.1l0.446,0.654l1.674,2.539l0.289,0.418l0.941,1.414              c0.576-1.099,1.179-2.172,1.728-3.272l-0.052-0.079l-0.995-1.466l-1.308-1.988l-0.785-1.204l-0.55-0.811l-3.01-4.581              l-1.361-2.041l4.817-2.094l6.307-2.775h0.026l0.288-0.13l7.93-3.455l2.224-3.429l0.419-0.654L1373.169,620.958z               M1262.506,681.053l-4.58-6.936l10.417-4.528l3.036-1.336l14.841-6.464l3.193,4.816l3.559,5.365l0.786,1.204l2.25,3.377              l0.76,1.152l-2.46,1.073l-25.86,11.255L1262.506,681.053z M1300.091,682.754l4.738,7.172l0.524,0.812l2.932,4.423              l0.497,0.759l-8.271,3.612l-10.521,4.581l-9.501,4.135l-1.911-2.879l-1.151-1.754l-4.162-6.307l-0.471-0.707              l-1.308-1.989l-1.518-2.304l5.365-2.329c0,0,0.027,0,0.052-0.026l19.369-8.455l3.507-1.518l1.335,2.016              L1300.091,682.754z M1321.947,654.748l4.607,6.989l1.02,1.544l0.524,0.785l-7.015,3.063c0,0,0,0-0.026,0l-8.036,3.507              l-10.993,4.789l-2.251,0.97l-0.314-0.471l-2.748-4.162l-3.089-4.685l-0.76-1.178l-3.611-5.445l28.293-12.328              L1321.947,654.748z"/>
											</g>
										</g>
										<g>
											<path style="fill:#B4B4B4;" d="M1334.423,684.462l-0.314,0.418h-0.026l-3.323,4.397l-5.602,7.329l-6.439,8.427             l-2.094,2.749l-5.471,7.172l-3.847,5.052l-3.377,4.423l-13.113,17.17c-1.388,1.832-2.827,3.664-4.293,5.443             c-1.204,1.492-2.46,2.985-3.717,4.45c-0.209,0.21-0.392,0.446-0.602,0.682c-0.969,1.099-1.963,2.224-2.984,3.297             c-0.471,0.524-0.942,1.047-1.44,1.57c-1.308,1.388-2.617,2.749-3.978,4.084c-0.419,0.418-0.812,0.837-1.23,1.23             c-0.942,0.915-1.885,1.832-2.853,2.722c-2.277,2.094-4.45,4.031-6.57,5.785c-31.017,25.807-45.987,15.809-53.106,2.722             c-5.287-9.607-6.282-20.913-6.282-20.913l29.183-15.234l19.421-10.129l27.352-14.292l2.904-1.517l14.396-7.513             l4.553-2.381l21.829-11.386l7.852-4.109L1334.423,684.462z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000039829560542106382270000008488814416606176668_" filterUnits="userSpaceOnUse" x="1203.764" y="684.462" width="130.659" height="102.47">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1203.764" y="684.462" width="130.659" height="102.47" id="SVGID_00000036937819331568140510000017258155121744937897_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000039829560542106382270000008488814416606176668_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000173872591755716835300000000374631396779439239_" filterUnits="userSpaceOnUse" x="1203.764" y="684.462" width="130.659" height="102.47">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1203.764" y="684.462" width="130.659" height="102.47" id="SVGID_00000036937819331568140510000017258155121744937897_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000173872591755716835300000000374631396779439239_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000032606073130383923620000005107400806639375775_" gradientUnits="userSpaceOnUse" x1="1203.7639" y1="735.6974" x2="1334.423" y2="735.6974">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000036937819331568140510000017258155121744937897_);fill:url(#SVGID_00000032606073130383923620000005107400806639375775_);" d="                M1334.423,684.462l-0.314,0.418h-0.026l-3.323,4.397l-5.602,7.329l-6.439,8.427l-2.094,2.749l-5.471,7.172                l-3.847,5.052l-3.377,4.423l-13.113,17.17c-1.388,1.832-2.827,3.664-4.293,5.443c-1.204,1.492-2.46,2.985-3.717,4.45                c-0.209,0.21-0.392,0.446-0.602,0.682c-0.969,1.099-1.963,2.224-2.984,3.297c-0.471,0.524-0.942,1.047-1.44,1.57                c-1.308,1.388-2.617,2.749-3.978,4.084c-0.419,0.418-0.812,0.837-1.23,1.23c-0.942,0.915-1.885,1.832-2.853,2.722                c-2.277,2.094-4.45,4.031-6.57,5.785c-31.017,25.807-45.987,15.809-53.106,2.722                c-5.287-9.607-6.282-20.913-6.282-20.913l29.183-15.234l19.421-10.129l27.352-14.292l2.904-1.517l14.396-7.513                l4.553-2.381l21.829-11.386l7.852-4.109L1334.423,684.462z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000078011663552859250090000009337507128025378967_" gradientUnits="userSpaceOnUse" x1="1203.7639" y1="735.6974" x2="1334.423" y2="735.6974">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000078011663552859250090000009337507128025378967_);" d="M1334.423,684.462              l-0.314,0.418h-0.026l-3.323,4.397l-5.602,7.329l-6.439,8.427l-2.094,2.749l-5.471,7.172l-3.847,5.052l-3.377,4.423              l-13.113,17.17c-1.388,1.832-2.827,3.664-4.293,5.443c-1.204,1.492-2.46,2.985-3.717,4.45              c-0.209,0.21-0.392,0.446-0.602,0.682c-0.969,1.099-1.963,2.224-2.984,3.297c-0.471,0.524-0.942,1.047-1.44,1.57              c-1.308,1.388-2.617,2.749-3.978,4.084c-0.419,0.418-0.812,0.837-1.23,1.23c-0.942,0.915-1.885,1.832-2.853,2.722              c-2.277,2.094-4.45,4.031-6.57,5.785c-31.017,25.807-45.987,15.809-53.106,2.722              c-5.287-9.607-6.282-20.913-6.282-20.913l29.183-15.234l19.421-10.129l27.352-14.292l2.904-1.517l14.396-7.513              l4.553-2.381l21.829-11.386l7.852-4.109L1334.423,684.462z"/>
											</g>
											<path style="fill:#676767;" d="M1362.481,629.943c-0.523,1.36-1.256,2.932-2.171,4.659             c-1.912,3.585-4.608,7.905-7.696,12.406c-0.104,0.158-0.236,0.315-0.34,0.472l-0.079,0.077             c-8.506,12.276-20.023,25.86-27.927,30.859c-0.236,0.158-0.445,0.289-0.654,0.419c-1.179,0.707-2.487,1.518-3.874,2.434             c-5.811,3.769-13.27,9.056-21.253,13.61c-1.701,0.969-3.429,1.911-5.183,2.801c-18.95,9.658-39.261,12.746-46.04-19.264             c-0.052-0.209-0.079-0.418-0.131-0.628v-0.026c-0.654-3.141-1.178-6.595-1.544-10.443             c-0.026-0.288-0.052-0.575-0.079-0.89v-1.518c0-0.026,0-0.026,0-0.026s0,0,0.026,0c0.131,0.052,0.785,0.262,1.911,0.549             c2.617,0.706,7.773,1.858,14.736,2.434c8.349,0.733,19.29,0.628,31.461-2.119c3.717-0.812,7.512-1.885,11.412-3.246             c12.432-4.398,25.65-11.752,38.449-23.791l0.026-0.026l0.053-0.052c0.602-0.576,1.203-1.125,1.753-1.623             c3.56-3.299,6.491-5.837,8.9-7.722C1363.057,622.352,1364.785,624.106,1362.481,629.943z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000164472970493179489230000015489485248405555363_" filterUnits="userSpaceOnUse" x="1245.51" y="624.783" width="117.896" height="79.508">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1245.51" y="624.783" width="117.896" height="79.508" id="SVGID_00000103946581287536815710000009724903905088242594_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000164472970493179489230000015489485248405555363_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000083806735692862295240000015261492701414892941_" filterUnits="userSpaceOnUse" x="1245.51" y="624.783" width="117.896" height="79.508">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1245.51" y="624.783" width="117.896" height="79.508" id="SVGID_00000103946581287536815710000009724903905088242594_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000083806735692862295240000015261492701414892941_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000078742900279376901240000002449028018640921508_" gradientUnits="userSpaceOnUse" x1="1245.5105" y1="664.5373" x2="1363.4064" y2="664.5373">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000103946581287536815710000009724903905088242594_);fill:url(#SVGID_00000078742900279376901240000002449028018640921508_);" d="                M1362.481,629.943c-0.523,1.36-1.256,2.932-2.171,4.659c-1.912,3.585-4.608,7.905-7.696,12.406                c-0.104,0.158-0.236,0.315-0.34,0.472l-0.079,0.077c-8.506,12.276-20.023,25.86-27.927,30.859                c-0.236,0.158-0.445,0.289-0.654,0.419c-1.179,0.707-2.487,1.518-3.874,2.434c-5.811,3.769-13.27,9.056-21.253,13.61                c-1.701,0.969-3.429,1.911-5.183,2.801c-18.95,9.658-39.261,12.746-46.04-19.264                c-0.052-0.209-0.079-0.418-0.131-0.628v-0.026c-0.654-3.141-1.178-6.595-1.544-10.443                c-0.026-0.288-0.052-0.575-0.079-0.89v-1.518c0-0.026,0-0.026,0-0.026s0,0,0.026,0                c0.131,0.052,0.785,0.262,1.911,0.549c2.617,0.706,7.773,1.858,14.736,2.434c8.349,0.733,19.29,0.628,31.461-2.119                c3.717-0.812,7.512-1.885,11.412-3.246c12.432-4.398,25.65-11.752,38.449-23.791l0.026-0.026l0.053-0.052                c0.602-0.576,1.203-1.125,1.753-1.623c3.56-3.299,6.491-5.837,8.9-7.722                C1363.057,622.352,1364.785,624.106,1362.481,629.943z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000161603555735506091080000017258376789017380271_" gradientUnits="userSpaceOnUse" x1="1245.5105" y1="664.5373" x2="1363.4064" y2="664.5373">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000161603555735506091080000017258376789017380271_);" d="M1362.481,629.943              c-0.523,1.36-1.256,2.932-2.171,4.659c-1.912,3.585-4.608,7.905-7.696,12.406c-0.104,0.158-0.236,0.315-0.34,0.472              l-0.079,0.077c-8.506,12.276-20.023,25.86-27.927,30.859c-0.236,0.158-0.445,0.289-0.654,0.419              c-1.179,0.707-2.487,1.518-3.874,2.434c-5.811,3.769-13.27,9.056-21.253,13.61c-1.701,0.969-3.429,1.911-5.183,2.801              c-18.95,9.658-39.261,12.746-46.04-19.264c-0.052-0.209-0.079-0.418-0.131-0.628v-0.026              c-0.654-3.141-1.178-6.595-1.544-10.443c-0.026-0.288-0.052-0.575-0.079-0.89v-1.518c0-0.026,0-0.026,0-0.026              s0,0,0.026,0c0.131,0.052,0.785,0.262,1.911,0.549c2.617,0.706,7.773,1.858,14.736,2.434              c8.349,0.733,19.29,0.628,31.461-2.119c3.717-0.812,7.512-1.885,11.412-3.246c12.432-4.398,25.65-11.752,38.449-23.791              l0.026-0.026l0.053-0.052c0.602-0.576,1.203-1.125,1.753-1.623c3.56-3.299,6.491-5.837,8.9-7.722              C1363.057,622.352,1364.785,624.106,1362.481,629.943z"/>
											</g>
											<path style="fill:#2C2C2C;" d="M1365.884,633.057c-1.806,4.057-3.769,8.14-5.916,12.276             c-0.55,1.099-1.151,2.171-1.727,3.271c-0.55,0.994-1.099,2.015-1.675,3.036c-0.733,1.308-1.492,2.617-2.251,3.926             c-4.135,7.04-8.794,14.108-13.977,21.148c-0.654,0.916-1.36,1.832-2.068,2.775c-1.256,1.649-2.538,3.324-3.847,4.973             l-0.314,0.418h-0.026c-1.23,1.545-2.487,3.063-3.769,4.58c-1.727,2.094-3.507,4.162-5.366,6.23             c-2.329,2.643-4.763,5.234-7.25,7.826c-0.759,0.785-1.518,1.57-2.329,2.356c-2.121,2.172-4.319,4.319-6.57,6.464             c-4.973,4.738-10.234,9.397-15.783,13.899c-0.994,0.811-1.99,1.623-2.983,2.407             c-7.225,5.759-14.919,11.282-23.086,16.568c-6.936,4.502-14.238,8.847-21.881,12.93             c-7.407,4.004-15.128,7.799-23.189,11.333c-4.476,1.963-8.402,3.324-11.83,4.109             c-27.823,6.595-22.353-23.033-10.287-53.63v-0.026c11.621-29.393,29.262-59.729,29.262-59.729l0.21,0.21l12.249,11.883             l4.162,4.056l1.491,1.414v0.026l0.262,0.262l6.333,6.02l0.367,0.366c0.261,0.261,0.55,0.524,0.838,0.785             c3.377,3.167,7.25,5.706,11.386,7.538c1.676,0.734,3.403,1.361,5.156,1.858c5.471,1.571,11.255,2.015,17.013,1.204             l0.315-0.053c1.596-0.235,3.193-0.549,4.736-0.968c1.492-0.393,2.958-0.89,4.372-1.439             c2.408-0.915,4.711-2.068,6.91-3.429c0.916-0.575,1.805-1.204,2.669-1.858c0.315-0.235,0.628-0.471,0.943-0.707             l10.259-8.113l4.371-3.429l7.486-5.916c0.287-0.235,0.602-0.471,0.89-0.759c0.838-0.654,1.649-1.414,2.434-2.224             c0.366-0.314,0.733-0.707,1.073-1.074l2.958-3.141l4.711-4.973l9.58-10.182l0.079-0.077l0.392-0.42l2.67-2.826             l2.277-2.435l4.502-4.763l3.717-3.926L1365.884,633.057z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000072993599980427869100000005280321000973458100_" filterUnits="userSpaceOnUse" x="1189.889" y="633.057" width="175.995" height="141.456">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="1189.889" y="633.057" width="175.995" height="141.456" id="SVGID_00000087386112712176798780000001922968227536877706_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000072993599980427869100000005280321000973458100_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000056408759287769245510000009484883346459263926_" filterUnits="userSpaceOnUse" x="1189.889" y="633.057" width="175.995" height="141.456">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="1189.889" y="633.057" width="175.995" height="141.456" id="SVGID_00000087386112712176798780000001922968227536877706_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000056408759287769245510000009484883346459263926_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000178906765070634715120000005735231009007517871_" gradientUnits="userSpaceOnUse" x1="1189.8887" y1="703.7849" x2="1365.8838" y2="703.7849">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000087386112712176798780000001922968227536877706_);fill:url(#SVGID_00000178906765070634715120000005735231009007517871_);" d="                M1365.884,633.057c-1.806,4.057-3.769,8.14-5.916,12.276c-0.55,1.099-1.151,2.171-1.727,3.271                c-0.55,0.994-1.099,2.015-1.675,3.036c-0.733,1.308-1.492,2.617-2.251,3.926c-4.135,7.04-8.794,14.108-13.977,21.148                c-0.654,0.916-1.36,1.832-2.068,2.775c-1.256,1.649-2.538,3.324-3.847,4.973l-0.314,0.418h-0.026                c-1.23,1.545-2.487,3.063-3.769,4.58c-1.727,2.094-3.507,4.162-5.366,6.23c-2.329,2.643-4.763,5.234-7.25,7.826                c-0.759,0.785-1.518,1.57-2.329,2.356c-2.121,2.172-4.319,4.319-6.57,6.464c-4.973,4.738-10.234,9.397-15.783,13.899                c-0.994,0.811-1.99,1.623-2.983,2.407c-7.225,5.759-14.919,11.282-23.086,16.568                c-6.936,4.502-14.238,8.847-21.881,12.93c-7.407,4.004-15.128,7.799-23.189,11.333                c-4.476,1.963-8.402,3.324-11.83,4.109c-27.823,6.595-22.353-23.033-10.287-53.63v-0.026                c11.621-29.393,29.262-59.729,29.262-59.729l0.21,0.21l12.249,11.883l4.162,4.056l1.491,1.414v0.026l0.262,0.262                l6.333,6.02l0.367,0.366c0.261,0.261,0.55,0.524,0.838,0.785c3.377,3.167,7.25,5.706,11.386,7.538                c1.676,0.734,3.403,1.361,5.156,1.858c5.471,1.571,11.255,2.015,17.013,1.204l0.315-0.053                c1.596-0.235,3.193-0.549,4.736-0.968c1.492-0.393,2.958-0.89,4.372-1.439c2.408-0.915,4.711-2.068,6.91-3.429                c0.916-0.575,1.805-1.204,2.669-1.858c0.315-0.235,0.628-0.471,0.943-0.707l10.259-8.113l4.371-3.429l7.486-5.916                c0.287-0.235,0.602-0.471,0.89-0.759c0.838-0.654,1.649-1.414,2.434-2.224c0.366-0.314,0.733-0.707,1.073-1.074                l2.958-3.141l4.711-4.973l9.58-10.182l0.079-0.077l0.392-0.42l2.67-2.826l2.277-2.435l4.502-4.763l3.717-3.926                L1365.884,633.057z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000006692009381344366280000012096395594059381128_" gradientUnits="userSpaceOnUse" x1="1189.8887" y1="703.7849" x2="1365.8838" y2="703.7849">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000006692009381344366280000012096395594059381128_);" d="M1365.884,633.057              c-1.806,4.057-3.769,8.14-5.916,12.276c-0.55,1.099-1.151,2.171-1.727,3.271c-0.55,0.994-1.099,2.015-1.675,3.036              c-0.733,1.308-1.492,2.617-2.251,3.926c-4.135,7.04-8.794,14.108-13.977,21.148c-0.654,0.916-1.36,1.832-2.068,2.775              c-1.256,1.649-2.538,3.324-3.847,4.973l-0.314,0.418h-0.026c-1.23,1.545-2.487,3.063-3.769,4.58              c-1.727,2.094-3.507,4.162-5.366,6.23c-2.329,2.643-4.763,5.234-7.25,7.826c-0.759,0.785-1.518,1.57-2.329,2.356              c-2.121,2.172-4.319,4.319-6.57,6.464c-4.973,4.738-10.234,9.397-15.783,13.899c-0.994,0.811-1.99,1.623-2.983,2.407              c-7.225,5.759-14.919,11.282-23.086,16.568c-6.936,4.502-14.238,8.847-21.881,12.93              c-7.407,4.004-15.128,7.799-23.189,11.333c-4.476,1.963-8.402,3.324-11.83,4.109              c-27.823,6.595-22.353-23.033-10.287-53.63v-0.026c11.621-29.393,29.262-59.729,29.262-59.729l0.21,0.21l12.249,11.883              l4.162,4.056l1.491,1.414v0.026l0.262,0.262l6.333,6.02l0.367,0.366c0.261,0.261,0.55,0.524,0.838,0.785              c3.377,3.167,7.25,5.706,11.386,7.538c1.676,0.734,3.403,1.361,5.156,1.858c5.471,1.571,11.255,2.015,17.013,1.204              l0.315-0.053c1.596-0.235,3.193-0.549,4.736-0.968c1.492-0.393,2.958-0.89,4.372-1.439              c2.408-0.915,4.711-2.068,6.91-3.429c0.916-0.575,1.805-1.204,2.669-1.858c0.315-0.235,0.628-0.471,0.943-0.707              l10.259-8.113l4.371-3.429l7.486-5.916c0.287-0.235,0.602-0.471,0.89-0.759c0.838-0.654,1.649-1.414,2.434-2.224              c0.366-0.314,0.733-0.707,1.073-1.074l2.958-3.141l4.711-4.973l9.58-10.182l0.079-0.077l0.392-0.42l2.67-2.826              l2.277-2.435l4.502-4.763l3.717-3.926L1365.884,633.057z"/>
											</g>
											<g>
												<path style="fill:#525252;" d="M1380.279,610.809l-1.387,2.12l-1.911,2.984l-3.062,4.711l-0.419,0.654l-2.224,3.429              l-5.392,8.349l-0.052,0.052l-3.114,4.816l-3.796,5.863l-1.911,2.984l-1.57,2.408l-2.644,4.109h-0.026l-1.492,2.33              c-0.393,0.602-0.784,1.204-1.178,1.78c0,0.026-0.052,0.052-0.052,0.052c-0.89,1.361-1.832,2.696-2.801,4.005              c-1.021,1.387-2.068,2.748-3.141,4.083c-0.942,1.178-1.911,2.303-2.879,3.429c-0.105,0.157-0.236,0.288-0.367,0.418              c-1.021,1.152-2.068,2.304-3.114,3.403c-0.236,0.261-0.497,0.55-0.76,0.812c-0.523,0.549-1.047,1.072-1.57,1.596              c-0.549,0.523-1.126,1.073-1.675,1.596c-1.099,1.047-2.199,2.067-3.35,3.063c-1.125,1.02-2.277,1.989-3.455,2.931              c-1.204,0.995-2.435,1.963-3.691,2.906c-0.393,0.288-0.785,0.575-1.178,0.864c-2.617,1.91-5.287,3.69-8.036,5.339              c-0.916,0.576-1.832,1.126-2.774,1.649c-0.026,0.026-0.078,0.052-0.105,0.052c-0.968,0.55-1.911,1.072-2.879,1.57              c-2.644,1.414-5.313,2.67-8.035,3.822c-1.805,0.759-3.638,1.492-5.47,2.146c-0.733,0.261-1.439,0.497-2.146,0.732              c-0.785,0.262-1.544,0.524-2.303,0.76c-0.732,0.209-1.465,0.419-2.198,0.628c-0.733,0.21-1.466,0.419-2.173,0.603              c-0.733,0.183-1.439,0.366-2.146,0.523c-0.287,0.052-0.575,0.131-0.837,0.184c-1.492,0.34-2.958,0.602-4.397,0.812              c-1.23,0.183-2.434,0.34-3.612,0.445c-0.733,0.078-1.439,0.13-2.146,0.157c-0.968,0.052-1.911,0.079-2.827,0.079              c-9.003,0-16.777-2.435-23.215-7.25c-0.549-0.419-1.099-0.838-1.623-1.282c-0.916-0.759-1.754-1.571-2.566-2.435              c-0.392-0.393-0.785-0.811-1.178-1.23c-0.55-0.628-1.047-1.257-1.544-1.911c-0.262-0.34-0.524-0.68-0.759-1.021              c-1.335-1.832-2.487-3.742-3.481-5.731c-0.158-0.289-0.314-0.576-0.445-0.864c-0.104-0.183-0.183-0.393-0.288-0.576              v-0.026c0-0.026,0-0.026,0-0.026c-0.236-0.523-0.471-1.021-0.707-1.518c-0.209-0.524-0.418-1.021-0.602-1.544              c-0.209-0.524-0.393-1.021-0.576-1.545c-0.183-0.497-0.367-1.02-0.523-1.517c-2.068-6.335-2.932-12.538-3.245-17.17              c0-0.158-0.026-0.315-0.026-0.471c-0.052-0.733-0.079-1.414-0.105-2.041c-0.131-3.063-0.026-5.053,0-5.366              c0.079-1.308,0.497-2.538,1.125-3.586c1.466-2.408,4.188-3.953,7.172-3.769c4.319,0.261,7.616,3.978,7.381,8.297              c0,0.236-0.026,0.471-0.026,0.733c-0.027,0.288-0.027,0.602-0.027,0.943c0,0.575,0,1.23,0.027,1.91              c0.026,0.549,0.052,1.1,0.078,1.701c0.026,0.445,0.053,0.89,0.105,1.361c0,0.105,0.026,0.209,0.026,0.314              c0.079,0.916,0.209,1.858,0.314,2.879c0.027,0.262,0.079,0.524,0.131,0.785c0.027,0.261,0.079,0.523,0.131,0.785              c0.079,0.602,0.183,1.203,0.287,1.805c0.026,0.106,0.052,0.21,0.079,0.315c0.105,0.549,0.236,1.099,0.367,1.648              c0.104,0.471,0.209,0.916,0.339,1.388c0.236,0.916,0.498,1.832,0.812,2.748c0.131,0.418,0.288,0.837,0.445,1.282              l0.55,1.413c0.366,0.891,0.759,1.78,1.203,2.644c0.105,0.209,0.209,0.418,0.34,0.628              c0.367,0.706,0.785,1.414,1.256,2.068c0.236,0.367,0.472,0.733,0.733,1.073c0.079,0.13,0.158,0.236,0.262,0.34              c0.235,0.34,0.497,0.681,0.785,0.995c0.34,0.392,0.68,0.758,1.047,1.125c0.026,0.026,0.026,0.053,0.052,0.079              c0.261,0.261,0.55,0.524,0.838,0.785c0.419,0.366,0.838,0.707,1.283,1.047c0.523,0.393,1.047,0.759,1.622,1.074              c0.262,0.156,0.524,0.314,0.812,0.444c0.288,0.183,0.602,0.34,0.916,0.445c0.236,0.131,0.472,0.236,0.733,0.315              c0.524,0.261,1.1,0.471,1.676,0.628c0.052,0.026,0.104,0.051,0.157,0.051c0.681,0.21,1.36,0.393,2.093,0.55              c0.052,0,0.105,0.026,0.158,0.026c0.628,0.13,1.308,0.235,1.989,0.314c0.419,0.078,0.864,0.104,1.309,0.13              c0.785,0.079,1.596,0.105,2.434,0.105c1.571,0,3.22-0.131,4.948-0.34c0.078,0,0.156,0,0.261-0.026              c0,0,0.027,0,0.053-0.026c1.073-0.131,2.146-0.315,3.271-0.524c0.262-0.052,0.55-0.105,0.839-0.158              c1.413-0.314,2.853-0.654,4.37-1.099c0.079-0.026,0.158-0.052,0.236-0.079c0.681-0.183,1.388-0.392,2.094-0.628              c0.445-0.13,0.89-0.288,1.334-0.445c0.628-0.21,1.257-0.419,1.911-0.654c1.178-0.42,2.356-0.864,3.507-1.335              c0.76-0.314,1.518-0.628,2.278-0.995c1.466-0.628,2.932-1.309,4.371-2.041c0.969-0.471,1.936-0.995,2.879-1.545              c0.393-0.183,0.785-0.418,1.151-0.628c0.733-0.392,1.44-0.812,2.146-1.256c1.99-1.204,3.953-2.487,5.863-3.848              c0.655-0.445,1.283-0.916,1.937-1.414h0.026c0.21-0.157,0.419-0.314,0.628-0.471c0.105-0.079,0.184-0.157,0.288-0.236              h0.026c0.969-0.733,1.937-1.492,2.854-2.251c0.105-0.104,0.209-0.183,0.314-0.288c0.995-0.785,1.963-1.623,2.905-2.513              c0.158-0.131,0.314-0.262,0.446-0.419c0.026,0,0.026,0,0.026,0c0.628-0.549,1.23-1.125,1.833-1.727              c0.025,0,0.025-0.026,0.052-0.026c0.025-0.027,0.078-0.079,0.104-0.105l0.288-0.288c0.733-0.681,1.44-1.413,2.146-2.12              c0.366-0.367,0.706-0.733,1.047-1.126c0.655-0.68,1.283-1.334,1.885-2.041c0.13-0.131,0.261-0.261,0.392-0.419              c0,0,0,0,0-0.025c0.498-0.55,0.969-1.1,1.44-1.65c0.603-0.68,1.152-1.361,1.702-2.068              c0.13-0.131,0.261-0.288,0.366-0.445c0.524-0.654,1.047-1.335,1.57-2.015c0.288-0.393,0.576-0.759,0.838-1.152              c0.497-0.654,0.968-1.334,1.439-2.015c0.655-0.916,1.282-1.859,1.911-2.827l5.418-8.375l0.026-0.026l0.053-0.052              l1.439-2.277l0.183-0.288l4.266-6.596l1.256-1.911l9.999-15.469l5.601-8.637v-0.026l0.759-1.152              c0.131-0.209,0.289-0.419,0.445-0.602c0-0.027,0-0.027,0-0.027c0.027-0.026,0.027-0.052,0.052-0.052              c0.158-0.209,0.341-0.418,0.55-0.602c0.577-0.576,1.231-1.047,1.938-1.414c0.025-0.026,0.077-0.026,0.104-0.052              c2.409-1.204,5.366-1.152,7.773,0.419C1381.588,602.302,1382.635,607.171,1380.279,610.809z"/>
											</g>
											<g style="opacity:0.4;">
												<g>
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000076572706501441272470000008328897932862235010_" filterUnits="userSpaceOnUse" x="1229.035" y="598.69" width="152.503" height="107.366">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1229.035" y="598.69" width="152.503" height="107.366" id="SVGID_00000068636640260493926070000001304326070453769909_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000076572706501441272470000008328897932862235010_);">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000039125125040699657340000006974728240143655313_" filterUnits="userSpaceOnUse" x="1229.035" y="598.69" width="152.503" height="107.366">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="1229.035" y="598.69" width="152.503" height="107.366" id="SVGID_00000068636640260493926070000001304326070453769909_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000039125125040699657340000006974728240143655313_);">
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000006667059745553141390000014993401705190693552_" gradientUnits="userSpaceOnUse" x1="1229.0353" y1="652.3727" x2="1381.5382" y2="652.3727">
																<stop offset="0" style="stop-color:#FFFFFF"/>
																<stop offset="0.0074" style="stop-color:#EEEEEE"/>
																<stop offset="0.0273" style="stop-color:#C4C4C4"/>
																<stop offset="0.0477" style="stop-color:#A2A2A2"/>
																<stop offset="0.0683" style="stop-color:#888888"/>
																<stop offset="0.0894" style="stop-color:#757575"/>
																<stop offset="0.1111" style="stop-color:#6A6A6A"/>
																<stop offset="0.1344" style="stop-color:#666666"/>
																<stop offset="0.3495" style="stop-color:#666666"/>
																<stop offset="0.4821" style="stop-color:#5D5D5D"/>
																<stop offset="0.7004" style="stop-color:#444444"/>
																<stop offset="0.8226" style="stop-color:#333333"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															
																<path style="mask:url(#SVGID_00000068636640260493926070000001304326070453769909_);fill:url(#SVGID_00000006667059745553141390000014993401705190693552_);" d="                 M1380.279,610.809l-1.387,2.12l-1.911,2.984l-3.062,4.711l-0.419,0.654l-2.224,3.429l-5.392,8.349l-0.052,0.052                 l-3.114,4.816l-3.796,5.863l-1.911,2.984l-1.57,2.408l-2.644,4.109h-0.026l-1.492,2.33                 c-0.393,0.602-0.784,1.204-1.178,1.78c0,0.026-0.052,0.052-0.052,0.052c-0.89,1.361-1.832,2.696-2.801,4.005                 c-1.021,1.387-2.068,2.748-3.141,4.083c-0.942,1.178-1.911,2.303-2.879,3.429c-0.105,0.157-0.236,0.288-0.367,0.418                 c-1.021,1.152-2.068,2.304-3.114,3.403c-0.236,0.261-0.497,0.55-0.76,0.812c-0.523,0.549-1.047,1.072-1.57,1.596                 c-0.549,0.523-1.126,1.073-1.675,1.596c-1.099,1.047-2.199,2.067-3.35,3.063c-1.125,1.02-2.277,1.989-3.455,2.931                 c-1.204,0.995-2.435,1.963-3.691,2.906c-0.393,0.288-0.785,0.575-1.178,0.838v0.026                 c-2.617,1.91-5.287,3.69-8.036,5.339c-0.916,0.576-1.832,1.126-2.774,1.649c-0.026,0.026-0.078,0.052-0.105,0.052                 c-0.968,0.55-1.911,1.072-2.879,1.57c-2.644,1.414-5.313,2.67-8.035,3.822c-1.805,0.759-3.638,1.492-5.47,2.146                 c-0.733,0.261-1.439,0.497-2.146,0.732c-0.785,0.262-1.544,0.524-2.303,0.76c-0.732,0.209-1.465,0.419-2.198,0.628                 c-0.733,0.21-1.466,0.419-2.173,0.603c-0.733,0.183-1.439,0.366-2.146,0.523c-0.287,0.052-0.575,0.131-0.837,0.184                 c-1.492,0.34-2.958,0.602-4.397,0.812c-1.23,0.183-2.434,0.34-3.612,0.445c-0.733,0.078-1.439,0.13-2.146,0.157                 c-0.968,0.052-1.911,0.079-2.827,0.079c-9.003,0-16.777-2.435-23.215-7.25c-0.549-0.419-1.099-0.838-1.623-1.282                 c-0.916-0.759-1.754-1.571-2.566-2.435c-0.392-0.393-0.785-0.811-1.178-1.23c-0.55-0.628-1.047-1.257-1.544-1.911                 c-0.262-0.34-0.524-0.68-0.759-1.021c-1.335-1.832-2.487-3.742-3.481-5.731c-0.158-0.289-0.314-0.576-0.445-0.864                 c-0.104-0.183-0.183-0.393-0.288-0.576v-0.026c0-0.026,0-0.026,0-0.026c-0.236-0.523-0.471-1.021-0.707-1.518                 c-0.209-0.524-0.418-1.021-0.602-1.544c-0.209-0.524-0.393-1.021-0.576-1.545c-0.183-0.497-0.367-1.02-0.523-1.517                 c-2.068-6.335-2.932-12.538-3.245-17.17c0-0.158-0.026-0.315-0.026-0.471c-0.052-0.733-0.079-1.414-0.105-2.041                 c-0.131-3.063-0.026-5.053,0-5.366c0.079-1.308,0.497-2.538,1.125-3.586c1.466-2.408,4.188-3.953,7.172-3.769                 c4.319,0.261,7.616,3.978,7.381,8.297c0,0.236-0.026,0.471-0.026,0.733c-0.027,0.288-0.027,0.602-0.027,0.943                 c0,0.575,0,1.23,0.027,1.91c0.026,0.549,0.052,1.1,0.078,1.701c0.026,0.445,0.053,0.89,0.105,1.361                 c0,0.105,0.026,0.209,0.026,0.314c0.079,0.916,0.209,1.858,0.314,2.879c0.027,0.262,0.079,0.524,0.131,0.785                 c0.027,0.261,0.052,0.523,0.105,0.785c0,0,0,0,0,0.026c0.105,0.576,0.209,1.178,0.314,1.779                 c0.026,0.106,0.052,0.21,0.079,0.315c0.105,0.549,0.236,1.099,0.367,1.648c0.104,0.471,0.209,0.916,0.339,1.388                 c0.236,0.916,0.498,1.832,0.812,2.748c0.131,0.418,0.288,0.837,0.445,1.282l0.55,1.413                 c0.366,0.891,0.759,1.78,1.203,2.644c0.105,0.209,0.209,0.418,0.34,0.628c0.367,0.706,0.785,1.414,1.256,2.068                 c0.236,0.367,0.472,0.733,0.733,1.073c0.079,0.13,0.158,0.236,0.262,0.34c0.235,0.34,0.497,0.681,0.785,0.995                 c0.34,0.392,0.68,0.758,1.047,1.125c0.026,0.026,0.026,0.053,0.052,0.079c0.261,0.261,0.55,0.524,0.838,0.785                 c0.419,0.366,0.838,0.707,1.283,1.047c0.523,0.393,1.047,0.759,1.622,1.074c0.262,0.156,0.524,0.314,0.812,0.444                 c0.288,0.183,0.602,0.34,0.916,0.445c0.236,0.131,0.472,0.236,0.733,0.315c0.524,0.261,1.1,0.471,1.676,0.628                 c0.052,0.026,0.104,0.051,0.157,0.051c0.681,0.21,1.36,0.393,2.093,0.55c0.052,0,0.105,0.026,0.158,0.026                 c0.628,0.13,1.308,0.235,1.989,0.314c0.419,0.078,0.864,0.104,1.309,0.13c0.785,0.079,1.596,0.105,2.434,0.105                 c1.571,0,3.22-0.131,4.948-0.34c0.078,0,0.156,0,0.261-0.026c0,0,0.027,0,0.053-0.026                 c1.073-0.131,2.146-0.315,3.271-0.524c0.262-0.052,0.55-0.105,0.839-0.158c1.413-0.314,2.853-0.654,4.37-1.099                 c0.079-0.026,0.158-0.052,0.236-0.079c0.681-0.183,1.388-0.392,2.094-0.628c0.445-0.13,0.89-0.288,1.334-0.445                 c0.628-0.21,1.257-0.419,1.911-0.654c1.178-0.42,2.356-0.864,3.507-1.335c0.76-0.314,1.518-0.628,2.278-0.995                 c1.466-0.628,2.932-1.309,4.371-2.041c0.969-0.471,1.936-0.995,2.879-1.545c0.393-0.183,0.785-0.418,1.151-0.628                 c0.733-0.392,1.44-0.812,2.146-1.256c1.99-1.204,3.953-2.487,5.863-3.848c0.655-0.445,1.283-0.916,1.937-1.414h0.026                 c0.21-0.157,0.419-0.314,0.628-0.471c0.105-0.079,0.184-0.157,0.288-0.236h0.026                 c0.969-0.733,1.937-1.492,2.854-2.251c0.105-0.104,0.209-0.183,0.314-0.288c0.995-0.811,1.963-1.648,2.905-2.513                 c0.158-0.131,0.314-0.262,0.446-0.419c0.026,0,0.026,0,0.026,0c0.628-0.549,1.23-1.125,1.833-1.727                 c0.025,0,0.025-0.026,0.052-0.026c0.025-0.027,0.078-0.079,0.104-0.105l0.288-0.288                 c0.733-0.681,1.44-1.413,2.146-2.12c0.366-0.367,0.706-0.733,1.047-1.126c0.655-0.68,1.283-1.334,1.885-2.041                 c0.13-0.131,0.261-0.261,0.392-0.419c0,0,0,0,0-0.025c0.498-0.55,0.969-1.1,1.44-1.65                 c0.603-0.68,1.152-1.361,1.702-2.068c0.13-0.131,0.261-0.288,0.366-0.445c0.524-0.654,1.047-1.335,1.57-2.015                 c0.288-0.393,0.576-0.759,0.838-1.152c0.497-0.654,0.968-1.334,1.439-2.015c0.655-0.916,1.282-1.859,1.911-2.827                 l5.418-8.375l0.026-0.026l0.053-0.052l1.439-2.277l0.183-0.288l4.266-6.596l1.256-1.911l9.999-15.469l5.601-8.637                 v-0.026l0.759-1.152c0.131-0.209,0.289-0.419,0.445-0.602c0-0.027,0-0.027,0-0.027                 c0.027-0.026,0.027-0.052,0.052-0.052c0.158-0.209,0.341-0.418,0.55-0.602c0.577-0.576,1.231-1.047,1.938-1.414                 c0.025-0.026,0.077-0.026,0.104-0.052c2.409-1.204,5.366-1.152,7.773,0.419                 C1381.588,602.302,1382.635,607.171,1380.279,610.809z"/>
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000146477550158845258670000001902768002581165749_" gradientUnits="userSpaceOnUse" x1="1229.0353" y1="652.3727" x2="1381.5382" y2="652.3727">
														<stop offset="0" style="stop-color:#FDFDFD"/>
														<stop offset="0.0027" style="stop-color:#F2F2F2"/>
														<stop offset="0.018" style="stop-color:#BABABA"/>
														<stop offset="0.0338" style="stop-color:#898989"/>
														<stop offset="0.0498" style="stop-color:#5F5F5F"/>
														<stop offset="0.0659" style="stop-color:#3C3C3C"/>
														<stop offset="0.0822" style="stop-color:#222222"/>
														<stop offset="0.0989" style="stop-color:#0F0F0F"/>
														<stop offset="0.116" style="stop-color:#040404"/>
														<stop offset="0.1344" style="stop-color:#000000"/>
														<stop offset="0.1553" style="stop-color:#090909"/>
														<stop offset="0.1899" style="stop-color:#222222"/>
														<stop offset="0.2339" style="stop-color:#4A4B4B"/>
														<stop offset="0.2855" style="stop-color:#828384"/>
														<stop offset="0.3429" style="stop-color:#C9CACB"/>
														<stop offset="0.3495" style="stop-color:#D1D3D4"/>
														<stop offset="0.4084" style="stop-color:#C8CACB"/>
														<stop offset="0.5058" style="stop-color:#B0B1B2"/>
														<stop offset="0.6294" style="stop-color:#888889"/>
														<stop offset="0.7733" style="stop-color:#525051"/>
														<stop offset="0.8226" style="stop-color:#3E3B3C"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													<path style="fill:url(#SVGID_00000146477550158845258670000001902768002581165749_);" d="M1380.279,610.809               l-1.387,2.12l-1.911,2.984l-3.062,4.711l-0.419,0.654l-2.224,3.429l-5.392,8.349l-0.052,0.052l-3.114,4.816               l-3.796,5.863l-1.911,2.984l-1.57,2.408l-2.644,4.109h-0.026l-1.492,2.33c-0.393,0.602-0.784,1.204-1.178,1.78               c0,0.026-0.052,0.052-0.052,0.052c-0.89,1.361-1.832,2.696-2.801,4.005c-1.021,1.387-2.068,2.748-3.141,4.083               c-0.942,1.178-1.911,2.303-2.879,3.429c-0.105,0.157-0.236,0.288-0.367,0.418c-1.021,1.152-2.068,2.304-3.114,3.403               c-0.236,0.261-0.497,0.55-0.76,0.812c-0.523,0.549-1.047,1.072-1.57,1.596c-0.549,0.523-1.126,1.073-1.675,1.596               c-1.099,1.047-2.199,2.067-3.35,3.063c-1.125,1.02-2.277,1.989-3.455,2.931c-1.204,0.995-2.435,1.963-3.691,2.906               c-0.393,0.288-0.785,0.575-1.178,0.838v0.026c-2.617,1.91-5.287,3.69-8.036,5.339               c-0.916,0.576-1.832,1.126-2.774,1.649c-0.026,0.026-0.078,0.052-0.105,0.052c-0.968,0.55-1.911,1.072-2.879,1.57               c-2.644,1.414-5.313,2.67-8.035,3.822c-1.805,0.759-3.638,1.492-5.47,2.146c-0.733,0.261-1.439,0.497-2.146,0.732               c-0.785,0.262-1.544,0.524-2.303,0.76c-0.732,0.209-1.465,0.419-2.198,0.628c-0.733,0.21-1.466,0.419-2.173,0.603               c-0.733,0.183-1.439,0.366-2.146,0.523c-0.287,0.052-0.575,0.131-0.837,0.184c-1.492,0.34-2.958,0.602-4.397,0.812               c-1.23,0.183-2.434,0.34-3.612,0.445c-0.733,0.078-1.439,0.13-2.146,0.157c-0.968,0.052-1.911,0.079-2.827,0.079               c-9.003,0-16.777-2.435-23.215-7.25c-0.549-0.419-1.099-0.838-1.623-1.282c-0.916-0.759-1.754-1.571-2.566-2.435               c-0.392-0.393-0.785-0.811-1.178-1.23c-0.55-0.628-1.047-1.257-1.544-1.911c-0.262-0.34-0.524-0.68-0.759-1.021               c-1.335-1.832-2.487-3.742-3.481-5.731c-0.158-0.289-0.314-0.576-0.445-0.864c-0.104-0.183-0.183-0.393-0.288-0.576               v-0.026c0-0.026,0-0.026,0-0.026c-0.236-0.523-0.471-1.021-0.707-1.518c-0.209-0.524-0.418-1.021-0.602-1.544               c-0.209-0.524-0.393-1.021-0.576-1.545c-0.183-0.497-0.367-1.02-0.523-1.517c-2.068-6.335-2.932-12.538-3.245-17.17               c0-0.158-0.026-0.315-0.026-0.471c-0.052-0.733-0.079-1.414-0.105-2.041c-0.131-3.063-0.026-5.053,0-5.366               c0.079-1.308,0.497-2.538,1.125-3.586c1.466-2.408,4.188-3.953,7.172-3.769c4.319,0.261,7.616,3.978,7.381,8.297               c0,0.236-0.026,0.471-0.026,0.733c-0.027,0.288-0.027,0.602-0.027,0.943c0,0.575,0,1.23,0.027,1.91               c0.026,0.549,0.052,1.1,0.078,1.701c0.026,0.445,0.053,0.89,0.105,1.361c0,0.105,0.026,0.209,0.026,0.314               c0.079,0.916,0.209,1.858,0.314,2.879c0.027,0.262,0.079,0.524,0.131,0.785c0.027,0.261,0.052,0.523,0.105,0.785               c0,0,0,0,0,0.026c0.105,0.576,0.209,1.178,0.314,1.779c0.026,0.106,0.052,0.21,0.079,0.315               c0.105,0.549,0.236,1.099,0.367,1.648c0.104,0.471,0.209,0.916,0.339,1.388c0.236,0.916,0.498,1.832,0.812,2.748               c0.131,0.418,0.288,0.837,0.445,1.282l0.55,1.413c0.366,0.891,0.759,1.78,1.203,2.644               c0.105,0.209,0.209,0.418,0.34,0.628c0.367,0.706,0.785,1.414,1.256,2.068c0.236,0.367,0.472,0.733,0.733,1.073               c0.079,0.13,0.158,0.236,0.262,0.34c0.235,0.34,0.497,0.681,0.785,0.995c0.34,0.392,0.68,0.758,1.047,1.125               c0.026,0.026,0.026,0.053,0.052,0.079c0.261,0.261,0.55,0.524,0.838,0.785c0.419,0.366,0.838,0.707,1.283,1.047               c0.523,0.393,1.047,0.759,1.622,1.074c0.262,0.156,0.524,0.314,0.812,0.444c0.288,0.183,0.602,0.34,0.916,0.445               c0.236,0.131,0.472,0.236,0.733,0.315c0.524,0.261,1.1,0.471,1.676,0.628c0.052,0.026,0.104,0.051,0.157,0.051               c0.681,0.21,1.36,0.393,2.093,0.55c0.052,0,0.105,0.026,0.158,0.026c0.628,0.13,1.308,0.235,1.989,0.314               c0.419,0.078,0.864,0.104,1.309,0.13c0.785,0.079,1.596,0.105,2.434,0.105c1.571,0,3.22-0.131,4.948-0.34               c0.078,0,0.156,0,0.261-0.026c0,0,0.027,0,0.053-0.026c1.073-0.131,2.146-0.315,3.271-0.524               c0.262-0.052,0.55-0.105,0.839-0.158c1.413-0.314,2.853-0.654,4.37-1.099c0.079-0.026,0.158-0.052,0.236-0.079               c0.681-0.183,1.388-0.392,2.094-0.628c0.445-0.13,0.89-0.288,1.334-0.445c0.628-0.21,1.257-0.419,1.911-0.654               c1.178-0.42,2.356-0.864,3.507-1.335c0.76-0.314,1.518-0.628,2.278-0.995c1.466-0.628,2.932-1.309,4.371-2.041               c0.969-0.471,1.936-0.995,2.879-1.545c0.393-0.183,0.785-0.418,1.151-0.628c0.733-0.392,1.44-0.812,2.146-1.256               c1.99-1.204,3.953-2.487,5.863-3.848c0.655-0.445,1.283-0.916,1.937-1.414h0.026c0.21-0.157,0.419-0.314,0.628-0.471               c0.105-0.079,0.184-0.157,0.288-0.236h0.026c0.969-0.733,1.937-1.492,2.854-2.251c0.105-0.104,0.209-0.183,0.314-0.288               c0.995-0.811,1.963-1.648,2.905-2.513c0.158-0.131,0.314-0.262,0.446-0.419c0.026,0,0.026,0,0.026,0               c0.628-0.549,1.23-1.125,1.833-1.727c0.025,0,0.025-0.026,0.052-0.026c0.025-0.027,0.078-0.079,0.104-0.105               l0.288-0.288c0.733-0.681,1.44-1.413,2.146-2.12c0.366-0.367,0.706-0.733,1.047-1.126               c0.655-0.68,1.283-1.334,1.885-2.041c0.13-0.131,0.261-0.261,0.392-0.419c0,0,0,0,0-0.025               c0.498-0.55,0.969-1.1,1.44-1.65c0.603-0.68,1.152-1.361,1.702-2.068c0.13-0.131,0.261-0.288,0.366-0.445               c0.524-0.654,1.047-1.335,1.57-2.015c0.288-0.393,0.576-0.759,0.838-1.152c0.497-0.654,0.968-1.334,1.439-2.015               c0.655-0.916,1.282-1.859,1.911-2.827l5.418-8.375l0.026-0.026l0.053-0.052l1.439-2.277l0.183-0.288l4.266-6.596               l1.256-1.911l9.999-15.469l5.601-8.637v-0.026l0.759-1.152c0.131-0.209,0.289-0.419,0.445-0.602               c0-0.027,0-0.027,0-0.027c0.027-0.026,0.027-0.052,0.052-0.052c0.158-0.209,0.341-0.418,0.55-0.602               c0.577-0.576,1.231-1.047,1.938-1.414c0.025-0.026,0.077-0.026,0.104-0.052c2.409-1.204,5.366-1.152,7.773,0.419               C1381.588,602.302,1382.635,607.171,1380.279,610.809z"/>
												</g>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M1293.592,692.628c0,3.22-0.34,6.308-0.969,9.239              c-8.245,2.801-15.809,4.188-22.641,4.188c-9.003,0-16.777-2.435-23.215-7.25c-0.549-0.419-1.099-0.838-1.623-1.282              c-0.183-1.597-0.261-3.245-0.261-4.895c0-6.282,1.282-12.144,3.533-17.144c1.727,4.241,4.24,8.14,7.8,10.784              c7.223,5.392,18.426,5.444,33.214,0.105c1.178-0.42,2.356-0.864,3.507-1.335              C1293.383,687.472,1293.592,690.011,1293.592,692.628z"/>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M1362.717,637.925l-7.277,11.255l-4.162,6.439c-4.188,6.491-8.978,12.511-14.291,17.982              c-1.047,1.099-2.146,2.146-3.245,3.192c-1.099,1.047-2.199,2.067-3.35,3.063c-1.125,1.02-2.277,1.989-3.455,2.931              c-0.995-1.334-1.885-2.8-2.67-4.371c-0.445-0.837-0.838-1.701-1.204-2.591c-1.099-2.565-1.911-5.34-2.46-8.271              c0.838-0.733,1.649-1.492,2.46-2.277l0.288-0.288c0.733-0.681,1.44-1.413,2.146-2.12              c1.649-1.701,3.245-3.455,4.764-5.261c0.603-0.68,1.152-1.361,1.702-2.068c0.13-0.131,0.261-0.288,0.366-0.445              c0.524-0.654,1.047-1.335,1.57-2.015c0.288-0.393,0.576-0.759,0.838-1.152c0.497-0.654,0.968-1.334,1.439-2.015              c0.655-0.916,1.282-1.859,1.911-2.827l5.418-8.375l0.026-0.026l1.492-2.329l5.706-8.795              c1.203,0.445,2.382,1.021,3.508,1.727c2.224,1.335,4.266,3.141,6.073,5.314c0.654,0.759,1.256,1.57,1.805,2.434              C1362.324,637.323,1362.534,637.611,1362.717,637.925z"/>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M1380.279,610.809l-1.387,2.12l-1.911,2.984l-3.062,4.711l-0.419,0.654l-2.224,3.429              l-5.392,8.349l-0.052,0.052l-3.114,4.816l-3.796,5.863l-1.911,2.984l-1.57,2.408c-0.969-0.628-1.884-1.335-2.775-2.121              c0,0-0.026-0.026-0.052-0.052c-0.444-0.392-0.89-0.811-1.308-1.23c-2.356-2.381-4.398-5.365-5.968-8.768              c-0.105-0.209-0.209-0.445-0.314-0.654l0.183-0.288l4.266-6.596l1.256-1.911l9.999-15.469l5.601-8.637v-0.026              l0.759-1.152c0.131-0.209,0.289-0.419,0.445-0.602c0-0.027,0-0.027,0-0.027c0.027-0.026,0.027-0.052,0.052-0.052              c0.158-0.209,0.341-0.418,0.55-0.602c0.577-0.576,1.231-1.047,1.938-1.414c0.025-0.026,0.077-0.026,0.104-0.052              c2.409-1.204,5.366-1.152,7.773,0.419C1381.588,602.302,1382.635,607.171,1380.279,610.809z"/>
											</g>
											<g style="opacity:0.04;">
												<path style="fill:#FFFCFF;" d="M1373.5,621.279l-2.224,3.429l-5.392,8.349c-1.806,4.057-3.769,8.14-5.916,12.276              c-0.55,1.099-1.151,2.171-1.727,3.271c-0.55,0.994-1.099,2.015-1.675,3.036c-0.733,1.308-1.492,2.617-2.251,3.926              c-4.135,7.04-8.794,14.108-13.977,21.148c-0.654,0.916-1.36,1.832-2.068,2.775c-1.256,1.649-2.538,3.324-3.847,4.973              l-0.314,0.418h-0.026l-3.323,4.397l-5.602,7.329l-6.439,8.427l-2.094,2.749l-5.471,7.172              c-0.837-0.837-1.622-1.701-2.355-2.618c-2.67-3.141-5.025-6.621-7.041-10.417c-0.078-0.105-0.13-0.209-0.184-0.314              c-0.366-0.654-0.733-1.361-1.072-2.068c-0.079-0.184-0.183-0.366-0.262-0.55c-0.628-1.335-1.23-2.696-1.753-4.109              c-0.21-0.497-0.393-0.994-0.576-1.518c-1.1-2.984-1.989-6.099-2.696-9.318c-0.183-0.837-0.339-1.675-0.471-2.538              c-0.183-0.891-0.315-1.807-0.446-2.722c-0.262-1.833-0.445-3.717-0.549-5.601c-0.027-0.419-0.052-0.812-0.079-1.23              c-0.052-1.152-0.078-2.304-0.078-3.455c0-0.471,0-0.942,0.026-1.413c0-0.445,0.026-0.889,0.026-1.334              c3.717-0.812,7.512-1.885,11.412-3.246c3.873-1.361,7.852-3.036,11.856-5.052c0.602-0.314,1.23-0.628,1.832-0.942              c1.047-0.55,2.121-1.125,3.193-1.754c0.943-0.523,1.858-1.073,2.801-1.648c0.602-0.367,1.203-0.76,1.832-1.151              c0.812-0.498,1.623-1.048,2.434-1.597c0.785-0.524,1.597-1.099,2.409-1.675c1.622-1.152,3.245-2.382,4.868-3.691              c2.407-1.937,4.816-4.031,7.224-6.281l0.026-0.026l0.053-0.052l1.439-2.277l0.183-0.288l4.266-6.596l1.256-1.911              l9.999-15.469c2.486,1.205,4.841,2.618,7.093,4.293c1.858,1.36,3.665,2.878,5.34,4.58              C1373.265,621.044,1373.396,621.174,1373.5,621.279z"/>
											</g>
											<g style="opacity:0.04;">
												<path style="fill:#FFFCFF;" d="M1325.157,696.607l-6.439,8.427l-2.094,2.749l-5.471,7.172l-3.847,5.052l-3.377,4.423              l-13.113,17.17c-1.388,1.832-2.827,3.664-4.293,5.443c-1.204,1.492-2.46,2.985-3.717,4.45              c-0.209,0.21-0.392,0.446-0.602,0.682c-0.969,1.099-1.963,2.224-2.984,3.297c-0.471,0.524-0.942,1.047-1.44,1.57              c-1.308,1.388-2.617,2.749-3.978,4.084c-0.419,0.418-0.812,0.837-1.23,1.23c-0.942,0.915-1.885,1.832-2.853,2.722              c-2.277,2.094-4.45,4.031-6.57,5.785c-6.78-2.617-12.904-7.015-18.087-12.72c-5.13-5.732-9.291-12.773-12.118-20.703              c-2.381-6.701-3.847-14.029-4.083-21.725c-0.052-0.916-0.078-1.805-0.078-2.721c0-10.601,2.198-20.573,6.098-29.236              v-0.026c0-0.026,0-0.026,0-0.026c1.832-4.135,4.057-7.957,6.595-11.411c1.309-1.754,2.67-3.402,4.109-4.974              c0.105-0.105,0.183-0.208,0.314-0.314c0.498-0.55,1.021-1.073,1.545-1.57c0.287,0.079,0.628,0.183,0.995,0.262              c1.178,0.287,2.722,0.628,4.554,0.968c2.512,0.47,5.627,0.916,9.187,1.204c0.942,0.079,1.885,0.158,2.905,0.21              c1.963,0.131,4.057,0.183,6.282,0.183c5.444-0.027,11.517-0.471,18.034-1.649c1.125-0.209,2.277-0.445,3.455-0.707              c0.261-0.026,0.524-0.104,0.785-0.156c3.717-0.812,7.512-1.885,11.412-3.246c2.407,1.963,4.685,4.214,6.752,6.674              c0.419,0.471,0.811,0.969,1.204,1.466c0.523,0.654,0.994,1.309,1.492,1.963h0.026c0.052,0.079,0.105,0.157,0.158,0.236              c1.439,2.016,2.801,4.161,4.004,6.413c0.366,0.654,0.707,1.335,1.047,2.015c0.837,1.701,1.623,3.455,2.329,5.261v0.026              c0.418,1.02,0.785,2.015,1.125,3.062c0.079,0.209,0.157,0.393,0.209,0.602c0.21,0.654,0.419,1.309,0.628,1.989              c0.34,1.152,0.628,2.303,0.916,3.482C1325.026,696.005,1325.105,696.293,1325.157,696.607z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M1273.805,761.125c-0.419,0.418-0.812,0.837-1.23,1.23              c-0.942,0.915-1.885,1.832-2.853,2.722c-2.277,2.094-4.45,4.031-6.57,5.785c-31.017,25.807-45.987,15.809-53.106,2.722              c-27.823,6.595-22.353-23.033-10.287-53.63v-0.026c6.439-3.22,13.453-4.973,20.782-4.973              c2.827,0,5.601,0.261,8.324,0.759c8.611,1.597,16.594,5.654,23.504,11.596c5.653,4.816,10.574,10.888,14.579,17.902              C1269.722,750.079,1272.024,755.419,1273.805,761.125z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M1244.83,658.76c0.026,0.445,0.053,0.89,0.105,1.361              c-5.392-3.193-10.34-6.936-14.71-11.176c1.466-2.408,4.188-3.953,7.172-3.769c4.319,0.261,7.616,3.978,7.381,8.297              c-0.052,0.969-0.078,2.172-0.026,3.586C1244.778,657.608,1244.804,658.158,1244.83,658.76z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M1370.176,599.528c-0.079,5.811-0.89,11.438-2.356,16.856              c-1.308,4.711-3.088,9.265-5.34,13.558c-8.113,15.651-22.195,28.293-39.574,35.465              c-3.56,1.519-7.277,2.775-11.098,3.769c-5.784,1.545-11.883,2.486-18.139,2.775c-1.465,0.105-2.958,0.131-4.449,0.131              c-9.476,0-18.584-1.492-27.038-4.215c8.349,0.733,19.29,0.628,31.461-2.119c3.717-0.812,7.512-1.885,11.412-3.246              c12.432-4.398,25.65-11.752,38.449-23.791l0.026-0.026l0.053-0.052l1.439-2.277l5.706-8.795l9.999-15.469l6.36-9.815              C1367.873,601.047,1368.946,600.131,1370.176,599.528z"/>
											</g>
										</g>
									</g>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000036930842021121466220000010822401352376195752_" filterUnits="userSpaceOnUse" x="1170.767" y="572.007" width="229.893" height="241.601">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1170.767" y="572.007" width="229.893" height="241.601" id="SVGID_00000130615994044716985340000012544498148941120133_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000036930842021121466220000010822401352376195752_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000130611193379385268620000014283010025391106748_" filterUnits="userSpaceOnUse" x="1170.767" y="572.007" width="229.893" height="241.601">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1170.767" y="572.007" width="229.893" height="241.601" id="SVGID_00000130615994044716985340000012544498148941120133_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000130611193379385268620000014283010025391106748_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000121962740008237432930000017553659754834533512_" gradientUnits="userSpaceOnUse" x1="1285.7136" y1="813.6085" x2="1285.7136" y2="572.0073">
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000130615994044716985340000012544498148941120133_);fill:url(#SVGID_00000121962740008237432930000017553659754834533512_);" d="             M1400.416,592.271c-0.079,0.418-0.158,0.864-0.236,1.309c-1.257,6.674-3.953,15.259-8.846,26.252             c-2.094,4.737-4.607,9.92-7.616,15.599c-2.54,4.79-5.392,9.946-8.664,15.469c-8.69,14.815-20.102,32.298-34.994,52.976             c-3.245,4.58-6.727,9.317-10.365,14.212c-1.727,2.33-3.455,4.607-5.208,6.858             c-74.7,97.052-143.589,110.532-152.698,59.388c-3.089-17.301,0.681-42.009,13.375-73.392             c3.821-9.423,8.245-18.243,13.165-26.462c0.471-0.785,0.942-1.57,1.466-2.355c4.764-7.773,9.972-14.997,15.521-21.724             c16.228-19.735,35.413-35.204,55.07-47.348c27.874-17.222,56.744-27.692,79.594-33.973             c12.04-3.324,22.431-5.471,30.126-6.805C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000022535432360022255270000005543279234218703027_" gradientUnits="userSpaceOnUse" x1="1285.7136" y1="813.6085" x2="1285.7136" y2="572.0073">
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="opacity:0.8;fill:url(#SVGID_00000022535432360022255270000005543279234218703027_);" d="M1400.416,592.271           c-0.079,0.418-0.158,0.864-0.236,1.309c-1.257,6.674-3.953,15.259-8.846,26.252c-2.094,4.737-4.607,9.92-7.616,15.599           c-2.54,4.79-5.392,9.946-8.664,15.469c-8.69,14.815-20.102,32.298-34.994,52.976c-3.245,4.58-6.727,9.317-10.365,14.212           c-1.727,2.33-3.455,4.607-5.208,6.858c-74.7,97.052-143.589,110.532-152.698,59.388           c-3.089-17.301,0.681-42.009,13.375-73.392c3.821-9.423,8.245-18.243,13.165-26.462c0.471-0.785,0.942-1.57,1.466-2.355           c4.764-7.773,9.972-14.997,15.521-21.724c16.228-19.735,35.413-35.204,55.07-47.348           c27.874-17.222,56.744-27.692,79.594-33.973c12.04-3.324,22.431-5.471,30.126-6.805           C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
									<path style="opacity:0.03;fill:#F2F8FC;" d="M1324.486,724.946c-74.7,97.052-143.589,110.532-152.698,59.388           c-3.089-17.301,0.681-42.009,13.375-73.392c3.821-9.423,8.245-18.243,13.165-26.462c5.837,0.026,11.595,0.262,17.249,0.733           c6.046,0.471,12.013,1.151,17.85,2.068c1.23,0.184,2.46,0.393,3.691,0.628c2.669,0.446,5.287,0.942,7.904,1.492           c2.12,0.444,4.213,0.916,6.307,1.438c5.758,1.361,11.386,2.932,16.856,4.738c1.726,0.549,3.402,1.125,5.077,1.728           c3.769,1.335,7.46,2.774,11.071,4.318c1.257,0.524,2.513,1.047,3.743,1.624c0.026,0,0.026,0.026,0.026,0.026           c0.628,0.261,1.257,0.549,1.885,0.837c1.073,0.498,2.146,0.994,3.22,1.518c0.104,0.052,0.235,0.105,0.34,0.157           c0.759,0.366,1.492,0.733,2.251,1.125c4.083,2.042,8.036,4.213,11.83,6.518c1.1,0.628,2.146,1.282,3.219,1.936           C1315.613,718.376,1320.167,721.57,1324.486,724.946z"/>
									<path style="opacity:0.03;fill:#F2F8FC;" d="M1340.059,703.877c-3.245,4.58-6.727,9.317-10.365,14.212           c-1.727,2.33-3.455,4.607-5.208,6.858c-74.7,97.052-143.589,110.532-152.698,59.388           c4.764-8.847,10.863-17.197,18.139-24.943c11.02-11.805,24.761-22.196,40.543-30.702c4.581-2.461,9.344-4.763,14.265-6.91           c10.443-4.554,21.619-8.323,33.345-11.255c2.67-0.628,5.34-1.256,8.061-1.805c0.995-0.21,1.963-0.419,2.958-0.603           c2.224-0.444,4.449-0.837,6.7-1.203c2.722-0.445,5.444-0.837,8.193-1.178c3.481-0.471,7.015-0.837,10.574-1.125           c0.733-0.053,1.492-0.131,2.225-0.183c0.471-0.027,0.968-0.053,1.466-0.079c0.34-0.027,0.681-0.052,1.047-0.079           c4.973-0.314,10.024-0.471,15.128-0.471C1336.317,703.798,1338.201,703.823,1340.059,703.877z"/>
									<path style="opacity:0.09;fill:#F2F8FC;" d="M1400.416,592.271c-0.079,0.418-0.158,0.864-0.236,1.309           c-1.257,6.674-3.953,15.259-8.846,26.252c-2.094,4.737-4.607,9.92-7.616,15.599c-3.717,0.21-7.486,0.315-11.281,0.341           c-0.733,0.026-1.492,0.026-2.251,0.026c-1.858,0-3.69-0.026-5.523-0.078h-0.524c-0.261,0-0.497,0-0.759-0.026           c-0.759,0-1.491-0.026-2.224-0.052c-0.471-0.026-0.916-0.052-1.361-0.079c-0.183,0-0.366,0-0.549-0.026           c-2.016-0.079-4.005-0.183-5.968-0.34c-1.387-0.079-2.774-0.183-4.135-0.315c-0.34-0.026-0.681-0.052-1.021-0.078           c-0.104-0.026-0.183-0.026-0.288-0.052h-0.026c-0.55-0.026-1.1-0.079-1.649-0.157c-1.151-0.105-2.303-0.236-3.455-0.367           c-11.333-1.309-22.326-3.402-32.822-6.203c-0.681-0.183-1.387-0.366-2.068-0.55c-0.681-0.208-1.361-0.392-2.042-0.575           h-0.026c-12.563-3.638-24.421-8.297-35.36-13.846c27.874-17.222,56.744-27.692,79.594-33.973           c12.04-3.324,22.431-5.471,30.126-6.805C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
								</g>
								<path style="opacity:0.42;fill:#F2F8FC;" d="M1400.416,592.271c-0.079,0.418-0.158,0.864-0.236,1.309          c-1.257,6.674-3.953,15.259-8.846,26.252c-5.078,4.607-10.287,9.03-15.574,13.296c-1.099,0.89-2.198,1.78-3.324,2.644          c-4.083,3.219-8.219,6.36-12.381,9.397c-0.052,0.052-0.078,0.077-0.13,0.077c-0.864,0.68-1.754,1.309-2.617,1.937          c-0.341,0.236-0.681,0.498-1.022,0.733h-0.026v0.026c0,0-0.026,0-0.026,0.026c-0.419,0.262-0.837,0.576-1.257,0.864          c-2.487,1.78-4.973,3.507-7.459,5.182c-1.256,0.864-2.513,1.727-3.795,2.566c-0.027,0-0.027,0.026-0.052,0.026          c-0.969,0.681-1.963,1.309-2.932,1.937c-4.92,3.246-9.815,6.36-14.683,9.318c-1.571,0.968-3.142,1.911-4.711,2.852          c-1.937,1.152-3.874,2.278-5.785,3.376c-4.241,2.488-8.454,4.869-12.59,7.119c-0.969,0.524-1.937,1.047-2.879,1.545          c-1.544,0.838-3.062,1.648-4.58,2.486c-0.785,0.393-1.57,0.812-2.329,1.204c-6.256,3.271-12.25,6.256-17.982,9.03          c-0.811,0.393-1.622,0.759-2.408,1.125c-0.209,0.13-0.418,0.235-0.627,0.314c-0.786,0.393-1.571,0.759-2.356,1.125          c-2.932,1.388-5.811,2.697-8.559,3.926c-1.544,0.706-3.062,1.388-4.528,2.042c-6.439,2.826-12.223,5.261-17.17,7.25          c-3.586,1.439-7.224,2.224-10.731,2.408c-10.365,0.497-19.368-4.241-21.174-12.041v-0.026          c-0.209-0.811-0.314-1.648-0.34-2.512c-0.105-3.9,0.236-8.637,1.282-14.317c1.204-6.701,3.324-14.736,6.727-24.368          c16.228-19.735,35.413-35.204,55.07-47.348c27.874-17.222,56.744-27.692,79.594-33.973          c12.04-3.324,22.431-5.471,30.126-6.805C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
								<path style="opacity:0.42;fill:#F2F8FC;" d="M1400.416,592.271c-0.079,0.418-0.158,0.864-0.236,1.309          c-0.471,0.209-0.942,0.418-1.414,0.628c-6.438,2.826-12.223,5.261-17.169,7.25c-0.498,0.21-0.995,0.393-1.492,0.55          c-2.434,0.864-4.895,1.414-7.303,1.676c-0.654,0.104-1.282,0.157-1.936,0.183c-0.288,0.027-0.576,0.027-0.864,0.027h-0.995          c-0.969-0.027-1.937-0.08-2.854-0.183c-0.13,0-0.261-0.026-0.392-0.026c-1.571-0.21-3.089-0.524-4.502-0.969          c-5.915-1.806-10.365-5.654-11.569-10.914c-0.209-0.812-0.314-1.648-0.341-2.512c-0.078-2.906,0.106-6.308,0.628-10.208          c12.04-3.324,22.431-5.471,30.126-6.805C1391.936,570.207,1402.405,580.44,1400.416,592.271z"/>
							</g>
							<g>
								<g>
									<path style="fill:#1C1C1C;" d="M2023.123,592.271c0.079,0.418,0.158,0.864,0.236,1.309           c1.256,6.674,3.952,15.259,8.846,26.252c2.094,4.737,4.607,9.92,7.617,15.599c2.539,4.79,5.391,9.946,8.663,15.469           c8.69,14.815,20.102,32.298,34.994,52.976c3.246,4.58,6.727,9.317,10.365,14.212c1.727,2.33,3.455,4.607,5.209,6.858           c74.7,97.052,143.588,110.532,152.697,59.388c3.089-17.301-0.681-42.009-13.375-73.392           c-3.822-9.423-8.244-18.243-13.165-26.462c-0.47-0.785-0.942-1.57-1.466-2.355c-4.764-7.773-9.972-14.997-15.521-21.724           c-16.228-19.735-35.413-35.204-55.069-47.348c-27.875-17.222-56.744-27.692-79.595-33.973           c-12.04-3.324-22.431-5.471-30.126-6.805C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
									<g>
										<g style="opacity:0.36;">
											<polygon style="fill:#D6EADC;" points="2178.158,656.131 2179.662,653.855 2053.832,599.023 2052.328,601.298                         "/>
											<path style="fill:#D6EADC;" d="M2044.647,612.929l1.911,2.984l7.119-10.732l1.676-2.538l0.654-0.969             c0-0.027,0-0.027,0-0.027l0.863-1.309l-3.035-1.308l-0.367,0.55l-0.104,0.157l-1.048,1.57L2044.647,612.929z"/>
											<g>
												<path style="fill:#FFFFFF;" d="M2049.611,620.617l0.419,0.654l2.224,3.429l7.931,3.455l0.288,0.13h0.026l6.308,2.775              l4.817,2.094l-1.361,2.041l-3.01,4.581l-0.551,0.811l-0.784,1.204l-1.309,1.988l-0.995,1.466l-0.052,0.079              c0.551,1.1,1.152,2.173,1.728,3.272l0.942-1.414l0.288-0.418l1.675-2.539l0.445-0.654l0.733-1.1l5.025-7.591              l0.261-0.418l0.76,0.34l0.681,0.288l2.224,0.968l24.63,10.731l-4.162,6.307l-4.083,6.151c0,0.027,0,0.027,0,0.027              l-1.675,2.538l-0.602,0.916l-9.632-4.188l-1.885-0.812l-3.062-1.335l-0.052-0.027l-7.041-3.089h-0.027l-3.768-1.649              c0.733,1.309,1.492,2.618,2.251,3.926l4.213,1.833c0,0.026,0.052,0.052,0.052,0.052l7.905,3.43h0.026l4.214,1.831              l5.287,2.303l-1.257,1.911l-0.314,0.498l-0.943,1.413l-0.026,0.026l-5.183,7.852c0.655,0.916,1.361,1.833,2.067,2.774              l2.854-4.293l2.592-3.978l1.387-2.067l0.497-0.76l1.361-2.068l3.533,1.545l4.894,2.146l5.706,2.487l0.76,0.34l1.1,0.472              h0.026l12.275,5.365l-1.178,1.779l-0.497,0.759l-4.528,6.858l-0.603,0.891l-3.062,4.632l-0.654,0.995l-11.385-4.973              l-8.061-3.508l-2.827-1.23l3.324,4.398l0.445,0.183l6.281,2.747l10.706,4.66l-4.371,6.648l-0.549,0.838l-0.471,0.681              l2.094,2.748l1.256-1.911l0.812-1.256l4.266-6.414l8.533,3.718l4.738,2.067l3.822,1.649l7.067,3.089l4.135,1.805              l-0.628,0.969l-9.893,14.946l-0.497-0.21l-14.291-6.229l3.376,4.423l10.417,4.528l2.538,1.1l0.943-1.414l10.313-15.625              l1.648-2.488l3.114-4.71l1.1-1.649l4.057-6.125l1.623-2.46l1.858-2.826l1.937-2.958l5.575-8.427l10.365-15.679              l3.481-5.261l0.34-0.497v-0.027l3.271-4.947l-2.513-1.099h-0.026l-0.289-0.131l-0.104-0.052l-0.105-0.052v0.026              l-0.367,0.549l-1.151,1.702l-7.094,10.757l-3.429,5.183l-7.748-3.377l-0.917-0.393l-19.63-8.558l10.522-15.94              l1.518-2.277l-3.036-1.309l-1.518,2.278l-10.521,15.913l-28.294-12.328l10.522-15.914l1.518-2.278v-0.026l-1.727-0.733              h-0.027l-1.282-0.576l-0.76,1.151l-0.759,1.126l-10.521,15.914l-23.609-10.287l-4.686-2.042l10.521-15.913l1.519-2.277              l-3.037-1.335v0.026l-1.518,2.251v0.027l-9.58,14.501l-0.942,1.413l-3.742-1.648l-0.288-0.105l-8.716-3.795              l-1.805-0.812h-0.027l-8.166-3.559L2049.611,620.617z M2155.091,690.03l-25.859-11.255l-2.46-1.073l0.759-1.152              l2.251-3.377l0.785-1.204l3.559-5.365l3.193-4.816l14.841,6.464l3.036,1.336l10.418,4.528l-4.581,6.936L2155.091,690.03              z M2123.945,681.995l1.335-2.016l3.507,1.518l19.369,8.455c0.026,0.026,0.052,0.026,0.052,0.026l5.366,2.329              l-1.519,2.304l-1.308,1.989l-0.472,0.707l-4.162,6.307l-1.151,1.754l-1.911,2.879l-9.501-4.135l-10.521-4.581              l-8.271-3.612l0.497-0.759l2.932-4.423l0.523-0.812l4.738-7.172L2123.945,681.995z M2105.99,648.126l28.293,12.328              l-3.612,5.445l-0.76,1.178l-3.089,4.685l-2.748,4.162l-0.314,0.471l-2.251-0.97l-10.993-4.789l-8.035-3.507              c-0.026,0-0.026,0-0.026,0l-7.015-3.063l0.524-0.785l1.02-1.544l4.607-6.989L2105.99,648.126z"/>
											</g>
											<g style="opacity:0.54;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000025412922903785280080000014410521844764282780_" filterUnits="userSpaceOnUse" x="2049.611" y="612.661" width="132.57" height="117.389">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2049.611" y="612.661" width="132.57" height="117.389" id="SVGID_00000165232227739001425790000016173527210346806425_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000025412922903785280080000014410521844764282780_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000034806088095997872850000011018021558286897553_" filterUnits="userSpaceOnUse" x="2049.611" y="612.661" width="132.57" height="117.389">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="2049.611" y="612.661" width="132.57" height="117.389" id="SVGID_00000165232227739001425790000016173527210346806425_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000034806088095997872850000011018021558286897553_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000155843161714139788300000009095236408734259081_" gradientUnits="userSpaceOnUse" x1="-4282.5854" y1="9538.542" x2="-4191.8584" y2="9538.542" gradientTransform="matrix(-0.4699 0.8827 0.8827 0.4699 -8308.6475 -78.2519)">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0575" style="stop-color:#F0F0F0"/>
															<stop offset="0.1676" style="stop-color:#CACACA"/>
															<stop offset="0.3174" style="stop-color:#8C8C8C"/>
															<stop offset="0.457" style="stop-color:#4D4D4D"/>
															<stop offset="0.517" style="stop-color:#565656"/>
															<stop offset="0.6162" style="stop-color:#6F6F6F"/>
															<stop offset="0.7423" style="stop-color:#989898"/>
															<stop offset="0.8887" style="stop-color:#D0D0D0"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000165232227739001425790000016173527210346806425_);fill:url(#SVGID_00000155843161714139788300000009095236408734259081_);" d="                M2049.611,620.617l0.419,0.654l2.224,3.429l7.931,3.455l0.288,0.13h0.026l6.308,2.775l4.817,2.094l-1.361,2.041                l-3.01,4.581l-0.551,0.811l-0.784,1.204l-1.309,1.988l-0.995,1.466l-0.052,0.079c0.551,1.1,1.152,2.173,1.728,3.272                l0.942-1.414l0.288-0.418l1.675-2.539l0.445-0.654l0.733-1.1l5.025-7.591l0.261-0.418l0.76,0.34l0.681,0.288                l2.224,0.968l24.63,10.731l-4.162,6.307l-4.083,6.151c0,0.027,0,0.027,0,0.027l-1.675,2.538l-0.602,0.916                l-9.632-4.188l-1.885-0.812l-3.062-1.335l-0.052-0.027l-7.041-3.089h-0.027l-3.768-1.649                c0.733,1.309,1.492,2.618,2.251,3.926l4.213,1.833c0,0.026,0.052,0.052,0.052,0.052l7.905,3.43h0.026l4.214,1.831                l5.287,2.303l-1.257,1.911l-0.314,0.498l-0.943,1.413l-0.026,0.026l-5.183,7.852c0.655,0.916,1.361,1.833,2.067,2.774                l2.854-4.293l2.592-3.978l1.387-2.067l0.497-0.76l1.361-2.068l3.533,1.545l4.894,2.146l5.706,2.487l0.76,0.34                l1.1,0.472h0.026l12.275,5.365l-1.178,1.779l-0.497,0.759l-4.528,6.858l-0.603,0.891l-3.062,4.632l-0.654,0.995                l-11.385-4.973l-8.061-3.508l-2.827-1.23l3.324,4.398l0.445,0.183l6.281,2.747l10.706,4.66l-4.371,6.648l-0.549,0.838                l-0.471,0.681l2.094,2.748l1.256-1.911l0.812-1.256l4.266-6.414l8.533,3.718l4.738,2.067l3.822,1.649l7.067,3.089                l4.135,1.805l-0.628,0.969l-9.893,14.946l-0.497-0.21l-14.291-6.229l3.376,4.423l10.417,4.528l2.538,1.1l0.943-1.414                l10.313-15.625l1.648-2.488l3.114-4.71l1.1-1.649l4.057-6.125l1.623-2.46l1.858-2.826l1.937-2.958l5.575-8.427                l10.365-15.679l3.481-5.261l0.34-0.497v-0.027l3.271-4.947l-2.513-1.099h-0.026l-0.289-0.131l-0.104-0.052                l-0.105-0.052v0.026l-0.367,0.549l-1.151,1.702l-7.094,10.757l-3.429,5.183l-7.748-3.377l-0.917-0.393l-19.63-8.558                l10.522-15.94l1.518-2.277l-3.036-1.309l-1.518,2.278l-10.521,15.913l-28.294-12.328l10.522-15.914l1.518-2.278                v-0.026l-1.727-0.733h-0.027l-1.282-0.576l-0.76,1.151l-0.759,1.126l-10.521,15.914l-23.609-10.287l-4.686-2.042                l10.521-15.913l1.519-2.277l-3.037-1.335v0.026l-1.518,2.251v0.027l-9.58,14.501l-0.942,1.413l-3.742-1.648                l-0.288-0.105l-8.716-3.795l-1.805-0.812h-0.027l-8.166-3.559L2049.611,620.617z M2155.091,690.03l-25.859-11.255                l-2.46-1.073l0.759-1.152l2.251-3.377l0.785-1.204l3.559-5.365l3.193-4.816l14.841,6.464l3.036,1.336l10.418,4.528                l-4.581,6.936L2155.091,690.03z M2123.945,681.995l1.335-2.016l3.507,1.518l19.369,8.455                c0.026,0.026,0.052,0.026,0.052,0.026l5.366,2.329l-1.519,2.304l-1.308,1.989l-0.472,0.707l-4.162,6.307l-1.151,1.754                l-1.911,2.879l-9.501-4.135l-10.521-4.581l-8.271-3.612l0.497-0.759l2.932-4.423l0.523-0.812l4.738-7.172                L2123.945,681.995z M2105.99,648.126l28.293,12.328l-3.612,5.445l-0.76,1.178l-3.089,4.685l-2.748,4.162l-0.314,0.471                l-2.251-0.97l-10.993-4.789l-8.035-3.507c-0.026,0-0.026,0-0.026,0l-7.015-3.063l0.524-0.785l1.02-1.544l4.607-6.989                L2105.99,648.126z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000106857153813732717520000009640539162799267731_" gradientUnits="userSpaceOnUse" x1="-4282.5854" y1="9538.542" x2="-4191.8584" y2="9538.542" gradientTransform="matrix(-0.4699 0.8827 0.8827 0.4699 -8308.6475 -78.2519)">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0475" style="stop-color:#EEF4F9"/>
													<stop offset="0.1384" style="stop-color:#C8DDEE"/>
													<stop offset="0.2634" style="stop-color:#89B7DC"/>
													<stop offset="0.4163" style="stop-color:#3483C3"/>
													<stop offset="0.457" style="stop-color:#1C75BC"/>
													<stop offset="0.5072" style="stop-color:#257BBF"/>
													<stop offset="0.5902" style="stop-color:#3E8AC6"/>
													<stop offset="0.6957" style="stop-color:#67A3D2"/>
													<stop offset="0.8195" style="stop-color:#A0C5E3"/>
													<stop offset="0.9573" style="stop-color:#E7F1F8"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000106857153813732717520000009640539162799267731_);" d="M2049.611,620.617l0.419,0.654              l2.224,3.429l7.931,3.455l0.288,0.13h0.026l6.308,2.775l4.817,2.094l-1.361,2.041l-3.01,4.581l-0.551,0.811              l-0.784,1.204l-1.309,1.988l-0.995,1.466l-0.052,0.079c0.551,1.1,1.152,2.173,1.728,3.272l0.942-1.414l0.288-0.418              l1.675-2.539l0.445-0.654l0.733-1.1l5.025-7.591l0.261-0.418l0.76,0.34l0.681,0.288l2.224,0.968l24.63,10.731              l-4.162,6.307l-4.083,6.151c0,0.027,0,0.027,0,0.027l-1.675,2.538l-0.602,0.916l-9.632-4.188l-1.885-0.812l-3.062-1.335              l-0.052-0.027l-7.041-3.089h-0.027l-3.768-1.649c0.733,1.309,1.492,2.618,2.251,3.926l4.213,1.833              c0,0.026,0.052,0.052,0.052,0.052l7.905,3.43h0.026l4.214,1.831l5.287,2.303l-1.257,1.911l-0.314,0.498l-0.943,1.413              l-0.026,0.026l-5.183,7.852c0.655,0.916,1.361,1.833,2.067,2.774l2.854-4.293l2.592-3.978l1.387-2.067l0.497-0.76              l1.361-2.068l3.533,1.545l4.894,2.146l5.706,2.487l0.76,0.34l1.1,0.472h0.026l12.275,5.365l-1.178,1.779l-0.497,0.759              l-4.528,6.858l-0.603,0.891l-3.062,4.632l-0.654,0.995l-11.385-4.973l-8.061-3.508l-2.827-1.23l3.324,4.398l0.445,0.183              l6.281,2.747l10.706,4.66l-4.371,6.648l-0.549,0.838l-0.471,0.681l2.094,2.748l1.256-1.911l0.812-1.256l4.266-6.414              l8.533,3.718l4.738,2.067l3.822,1.649l7.067,3.089l4.135,1.805l-0.628,0.969l-9.893,14.946l-0.497-0.21l-14.291-6.229              l3.376,4.423l10.417,4.528l2.538,1.1l0.943-1.414l10.313-15.625l1.648-2.488l3.114-4.71l1.1-1.649l4.057-6.125              l1.623-2.46l1.858-2.826l1.937-2.958l5.575-8.427l10.365-15.679l3.481-5.261l0.34-0.497v-0.027l3.271-4.947              l-2.513-1.099h-0.026l-0.289-0.131l-0.104-0.052l-0.105-0.052v0.026l-0.367,0.549l-1.151,1.702l-7.094,10.757              l-3.429,5.183l-7.748-3.377l-0.917-0.393l-19.63-8.558l10.522-15.94l1.518-2.277l-3.036-1.309l-1.518,2.278              l-10.521,15.913l-28.294-12.328l10.522-15.914l1.518-2.278v-0.026l-1.727-0.733h-0.027l-1.282-0.576l-0.76,1.151              l-0.759,1.126l-10.521,15.914l-23.609-10.287l-4.686-2.042l10.521-15.913l1.519-2.277l-3.037-1.335v0.026l-1.518,2.251              v0.027l-9.58,14.501l-0.942,1.413l-3.742-1.648l-0.288-0.105l-8.716-3.795l-1.805-0.812h-0.027l-8.166-3.559              L2049.611,620.617z M2155.091,690.03l-25.859-11.255l-2.46-1.073l0.759-1.152l2.251-3.377l0.785-1.204l3.559-5.365              l3.193-4.816l14.841,6.464l3.036,1.336l10.418,4.528l-4.581,6.936L2155.091,690.03z M2123.945,681.995l1.335-2.016              l3.507,1.518l19.369,8.455c0.026,0.026,0.052,0.026,0.052,0.026l5.366,2.329l-1.519,2.304l-1.308,1.989l-0.472,0.707              l-4.162,6.307l-1.151,1.754l-1.911,2.879l-9.501-4.135l-10.521-4.581l-8.271-3.612l0.497-0.759l2.932-4.423l0.523-0.812              l4.738-7.172L2123.945,681.995z M2105.99,648.126l28.293,12.328l-3.612,5.445l-0.76,1.178l-3.089,4.685l-2.748,4.162              l-0.314,0.471l-2.251-0.97l-10.993-4.789l-8.035-3.507c-0.026,0-0.026,0-0.026,0l-7.015-3.063l0.524-0.785l1.02-1.544              l4.607-6.989L2105.99,648.126z"/>
											</g>
										</g>
										<g>
											<path style="fill:#B4B4B4;" d="M2089.116,684.462l0.314,0.418h0.026l3.324,4.397l5.601,7.329l6.438,8.427l2.094,2.749             l5.471,7.172l3.847,5.052l3.376,4.423l13.114,17.17c1.387,1.832,2.826,3.664,4.292,5.443             c1.204,1.492,2.46,2.985,3.717,4.45c0.209,0.21,0.393,0.446,0.602,0.682c0.969,1.099,1.963,2.224,2.984,3.297             c0.472,0.524,0.942,1.047,1.44,1.57c1.309,1.388,2.617,2.749,3.978,4.084c0.419,0.418,0.812,0.837,1.23,1.23             c0.943,0.915,1.885,1.832,2.854,2.722c2.277,2.094,4.449,4.031,6.569,5.785c31.017,25.807,45.988,15.809,53.107,2.722             c5.286-9.607,6.281-20.913,6.281-20.913l-29.183-15.234l-19.42-10.129l-27.352-14.292l-2.905-1.517l-14.396-7.513             l-4.554-2.381l-21.829-11.386l-7.852-4.109L2089.116,684.462z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000160190528621077278960000014487620842300849307_" filterUnits="userSpaceOnUse" x="2089.116" y="684.462" width="130.659" height="102.47">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2089.116" y="684.462" width="130.659" height="102.47" id="SVGID_00000114059043274184889750000005729728973546273725_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000160190528621077278960000014487620842300849307_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000083780323947150513200000013130478358318737854_" filterUnits="userSpaceOnUse" x="2089.116" y="684.462" width="130.659" height="102.47">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="2089.116" y="684.462" width="130.659" height="102.47" id="SVGID_00000114059043274184889750000005729728973546273725_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000083780323947150513200000013130478358318737854_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000049929765589559681460000013399440872307072422_" gradientUnits="userSpaceOnUse" x1="-5140.2715" y1="735.6974" x2="-5009.6123" y2="735.6974" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000114059043274184889750000005729728973546273725_);fill:url(#SVGID_00000049929765589559681460000013399440872307072422_);" d="                M2089.116,684.462l0.314,0.418h0.026l3.324,4.397l5.601,7.329l6.438,8.427l2.094,2.749l5.471,7.172l3.847,5.052                l3.376,4.423l13.114,17.17c1.387,1.832,2.826,3.664,4.292,5.443c1.204,1.492,2.46,2.985,3.717,4.45                c0.209,0.21,0.393,0.446,0.602,0.682c0.969,1.099,1.963,2.224,2.984,3.297c0.472,0.524,0.942,1.047,1.44,1.57                c1.309,1.388,2.617,2.749,3.978,4.084c0.419,0.418,0.812,0.837,1.23,1.23c0.943,0.915,1.885,1.832,2.854,2.722                c2.277,2.094,4.449,4.031,6.569,5.785c31.017,25.807,45.988,15.809,53.107,2.722                c5.286-9.607,6.281-20.913,6.281-20.913l-29.183-15.234l-19.42-10.129l-27.352-14.292l-2.905-1.517l-14.396-7.513                l-4.554-2.381l-21.829-11.386l-7.852-4.109L2089.116,684.462z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000179639408654715329420000009099679174623552900_" gradientUnits="userSpaceOnUse" x1="-5140.2715" y1="735.6974" x2="-5009.6123" y2="735.6974" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000179639408654715329420000009099679174623552900_);" d="M2089.116,684.462l0.314,0.418              h0.026l3.324,4.397l5.601,7.329l6.438,8.427l2.094,2.749l5.471,7.172l3.847,5.052l3.376,4.423l13.114,17.17              c1.387,1.832,2.826,3.664,4.292,5.443c1.204,1.492,2.46,2.985,3.717,4.45c0.209,0.21,0.393,0.446,0.602,0.682              c0.969,1.099,1.963,2.224,2.984,3.297c0.472,0.524,0.942,1.047,1.44,1.57c1.309,1.388,2.617,2.749,3.978,4.084              c0.419,0.418,0.812,0.837,1.23,1.23c0.943,0.915,1.885,1.832,2.854,2.722c2.277,2.094,4.449,4.031,6.569,5.785              c31.017,25.807,45.988,15.809,53.107,2.722c5.286-9.607,6.281-20.913,6.281-20.913l-29.183-15.234l-19.42-10.129              l-27.352-14.292l-2.905-1.517l-14.396-7.513l-4.554-2.381l-21.829-11.386l-7.852-4.109L2089.116,684.462z"/>
											</g>
											<path style="fill:#676767;" d="M2061.058,629.943c0.523,1.36,1.256,2.932,2.172,4.659             c1.911,3.585,4.607,7.905,7.696,12.406c0.104,0.158,0.236,0.315,0.34,0.472l0.079,0.077             c8.506,12.276,20.023,25.86,27.927,30.859c0.236,0.158,0.446,0.289,0.654,0.419c1.178,0.707,2.488,1.518,3.874,2.434             c5.811,3.769,13.271,9.056,21.254,13.61c1.701,0.969,3.429,1.911,5.182,2.801c18.95,9.658,39.26,12.746,46.039-19.264             c0.053-0.209,0.079-0.418,0.131-0.628v-0.026c0.655-3.141,1.178-6.595,1.544-10.443c0.027-0.288,0.052-0.575,0.079-0.89             v-1.518c0-0.026,0-0.026,0-0.026s0,0-0.027,0c-0.131,0.052-0.785,0.262-1.911,0.549             c-2.617,0.706-7.773,1.858-14.735,2.434c-8.35,0.733-19.291,0.628-31.461-2.119c-3.718-0.812-7.513-1.885-11.412-3.246             c-12.433-4.398-25.651-11.752-38.45-23.791l-0.026-0.026l-0.052-0.052c-0.602-0.576-1.204-1.125-1.754-1.623             c-3.56-3.299-6.491-5.837-8.9-7.722C2060.482,622.352,2058.754,624.106,2061.058,629.943z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000067214499569775968070000004953428528918816432_" filterUnits="userSpaceOnUse" x="2060.133" y="624.783" width="117.896" height="79.508">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2060.133" y="624.783" width="117.896" height="79.508" id="SVGID_00000038383061731096110290000005990276510344821659_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000067214499569775968070000004953428528918816432_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000032609770046855278500000017187914982386502309_" filterUnits="userSpaceOnUse" x="2060.133" y="624.783" width="117.896" height="79.508">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="2060.133" y="624.783" width="117.896" height="79.508" id="SVGID_00000038383061731096110290000005990276510344821659_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000032609770046855278500000017187914982386502309_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000061438006747115032630000011189368585931676345_" gradientUnits="userSpaceOnUse" x1="-5098.5244" y1="664.5373" x2="-4980.6289" y2="664.5373" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000038383061731096110290000005990276510344821659_);fill:url(#SVGID_00000061438006747115032630000011189368585931676345_);" d="                M2061.058,629.943c0.523,1.36,1.256,2.932,2.172,4.659c1.911,3.585,4.607,7.905,7.696,12.406                c0.104,0.158,0.236,0.315,0.34,0.472l0.079,0.077c8.506,12.276,20.023,25.86,27.927,30.859                c0.236,0.158,0.446,0.289,0.654,0.419c1.178,0.707,2.488,1.518,3.874,2.434c5.811,3.769,13.271,9.056,21.254,13.61                c1.701,0.969,3.429,1.911,5.182,2.801c18.95,9.658,39.26,12.746,46.039-19.264c0.053-0.209,0.079-0.418,0.131-0.628                v-0.026c0.655-3.141,1.178-6.595,1.544-10.443c0.027-0.288,0.052-0.575,0.079-0.89v-1.518c0-0.026,0-0.026,0-0.026                s0,0-0.027,0c-0.131,0.052-0.785,0.262-1.911,0.549c-2.617,0.706-7.773,1.858-14.735,2.434                c-8.35,0.733-19.291,0.628-31.461-2.119c-3.718-0.812-7.513-1.885-11.412-3.246                c-12.433-4.398-25.651-11.752-38.45-23.791l-0.026-0.026l-0.052-0.052c-0.602-0.576-1.204-1.125-1.754-1.623                c-3.56-3.299-6.491-5.837-8.9-7.722C2060.482,622.352,2058.754,624.106,2061.058,629.943z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000004538514934254760750000010513872613023116715_" gradientUnits="userSpaceOnUse" x1="-5098.5244" y1="664.5373" x2="-4980.6289" y2="664.5373" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000004538514934254760750000010513872613023116715_);" d="M2061.058,629.943              c0.523,1.36,1.256,2.932,2.172,4.659c1.911,3.585,4.607,7.905,7.696,12.406c0.104,0.158,0.236,0.315,0.34,0.472              l0.079,0.077c8.506,12.276,20.023,25.86,27.927,30.859c0.236,0.158,0.446,0.289,0.654,0.419              c1.178,0.707,2.488,1.518,3.874,2.434c5.811,3.769,13.271,9.056,21.254,13.61c1.701,0.969,3.429,1.911,5.182,2.801              c18.95,9.658,39.26,12.746,46.039-19.264c0.053-0.209,0.079-0.418,0.131-0.628v-0.026              c0.655-3.141,1.178-6.595,1.544-10.443c0.027-0.288,0.052-0.575,0.079-0.89v-1.518c0-0.026,0-0.026,0-0.026s0,0-0.027,0              c-0.131,0.052-0.785,0.262-1.911,0.549c-2.617,0.706-7.773,1.858-14.735,2.434c-8.35,0.733-19.291,0.628-31.461-2.119              c-3.718-0.812-7.513-1.885-11.412-3.246c-12.433-4.398-25.651-11.752-38.45-23.791l-0.026-0.026l-0.052-0.052              c-0.602-0.576-1.204-1.125-1.754-1.623c-3.56-3.299-6.491-5.837-8.9-7.722              C2060.482,622.352,2058.754,624.106,2061.058,629.943z"/>
											</g>
											<path style="fill:#2C2C2C;" d="M2057.655,633.057c1.806,4.057,3.769,8.14,5.916,12.276             c0.55,1.099,1.151,2.171,1.727,3.271c0.55,0.994,1.1,2.015,1.675,3.036c0.733,1.308,1.491,2.617,2.251,3.926             c4.135,7.04,8.794,14.108,13.977,21.148c0.654,0.916,1.361,1.832,2.068,2.775c1.257,1.649,2.539,3.324,3.847,4.973             l0.314,0.418h0.026c1.231,1.545,2.487,3.063,3.769,4.58c1.727,2.094,3.507,4.162,5.366,6.23             c2.329,2.643,4.763,5.234,7.25,7.826c0.758,0.785,1.517,1.57,2.329,2.356c2.12,2.172,4.318,4.319,6.57,6.464             c4.973,4.738,10.234,9.397,15.782,13.899c0.994,0.811,1.99,1.623,2.984,2.407c7.224,5.759,14.919,11.282,23.085,16.568             c6.936,4.502,14.239,8.847,21.882,12.93c7.406,4.004,15.127,7.799,23.189,11.333c4.476,1.963,8.402,3.324,11.831,4.109             c27.822,6.595,22.352-23.033,10.286-53.63v-0.026c-11.621-29.393-29.262-59.729-29.262-59.729l-0.21,0.21l-12.249,11.883             l-4.162,4.056l-1.491,1.414v0.026l-0.261,0.262l-6.334,6.02l-0.367,0.366c-0.261,0.261-0.55,0.524-0.838,0.785             c-3.376,3.167-7.25,5.706-11.385,7.538c-1.676,0.734-3.402,1.361-5.156,1.858c-5.471,1.571-11.254,2.015-17.013,1.204             l-0.314-0.053c-1.596-0.235-3.193-0.549-4.737-0.968c-1.492-0.393-2.958-0.89-4.372-1.439             c-2.408-0.915-4.711-2.068-6.909-3.429c-0.916-0.575-1.806-1.204-2.67-1.858c-0.314-0.235-0.627-0.471-0.942-0.707             l-10.26-8.113l-4.371-3.429l-7.486-5.916c-0.287-0.235-0.602-0.471-0.889-0.759c-0.838-0.654-1.649-1.414-2.435-2.224             c-0.366-0.314-0.733-0.707-1.072-1.074l-2.958-3.141l-4.71-4.973l-9.58-10.182l-0.079-0.077l-0.392-0.42l-2.67-2.826             l-2.277-2.435l-4.502-4.763l-3.717-3.926L2057.655,633.057z"/>
											<g style="opacity:0.66;">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000175319695176880028210000012215517601134935440_" filterUnits="userSpaceOnUse" x="2057.655" y="633.057" width="175.995" height="141.456">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2057.655" y="633.057" width="175.995" height="141.456" id="SVGID_00000137092953354541274820000001978271819392518329_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000175319695176880028210000012215517601134935440_);">
														<defs>
															
																<filter id="Adobe_OpacityMaskFilter_00000116940067222836153070000005340685394634835101_" filterUnits="userSpaceOnUse" x="2057.655" y="633.057" width="175.995" height="141.456">
																<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																<feBlend in="SourceGraphic" in2="back" mode="normal"/>
															</filter>
														</defs>
														
															<mask maskUnits="userSpaceOnUse" x="2057.655" y="633.057" width="175.995" height="141.456" id="SVGID_00000137092953354541274820000001978271819392518329_">
															<g style="filter:url(#Adobe_OpacityMaskFilter_00000116940067222836153070000005340685394634835101_);">
															</g>
														</mask>
														
															<linearGradient id="SVGID_00000099660419015895312840000009821162832259884732_" gradientUnits="userSpaceOnUse" x1="-5154.1465" y1="703.7849" x2="-4978.1514" y2="703.7849" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
															<stop offset="0" style="stop-color:#FFFFFF"/>
															<stop offset="0.0074" style="stop-color:#EEEEEE"/>
															<stop offset="0.0273" style="stop-color:#C4C4C4"/>
															<stop offset="0.0477" style="stop-color:#A2A2A2"/>
															<stop offset="0.0683" style="stop-color:#888888"/>
															<stop offset="0.0894" style="stop-color:#757575"/>
															<stop offset="0.1111" style="stop-color:#6A6A6A"/>
															<stop offset="0.1344" style="stop-color:#666666"/>
															<stop offset="0.3495" style="stop-color:#666666"/>
															<stop offset="0.4821" style="stop-color:#5D5D5D"/>
															<stop offset="0.7004" style="stop-color:#444444"/>
															<stop offset="0.8226" style="stop-color:#333333"/>
															<stop offset="1" style="stop-color:#FFFFFF"/>
														</linearGradient>
														
															<path style="mask:url(#SVGID_00000137092953354541274820000001978271819392518329_);fill:url(#SVGID_00000099660419015895312840000009821162832259884732_);" d="                M2057.655,633.057c1.806,4.057,3.769,8.14,5.916,12.276c0.55,1.099,1.151,2.171,1.727,3.271                c0.55,0.994,1.1,2.015,1.675,3.036c0.733,1.308,1.491,2.617,2.251,3.926c4.135,7.04,8.794,14.108,13.977,21.148                c0.654,0.916,1.361,1.832,2.068,2.775c1.257,1.649,2.539,3.324,3.847,4.973l0.314,0.418h0.026                c1.231,1.545,2.487,3.063,3.769,4.58c1.727,2.094,3.507,4.162,5.366,6.23c2.329,2.643,4.763,5.234,7.25,7.826                c0.758,0.785,1.517,1.57,2.329,2.356c2.12,2.172,4.318,4.319,6.57,6.464c4.973,4.738,10.234,9.397,15.782,13.899                c0.994,0.811,1.99,1.623,2.984,2.407c7.224,5.759,14.919,11.282,23.085,16.568c6.936,4.502,14.239,8.847,21.882,12.93                c7.406,4.004,15.127,7.799,23.189,11.333c4.476,1.963,8.402,3.324,11.831,4.109                c27.822,6.595,22.352-23.033,10.286-53.63v-0.026c-11.621-29.393-29.262-59.729-29.262-59.729l-0.21,0.21                l-12.249,11.883l-4.162,4.056l-1.491,1.414v0.026l-0.261,0.262l-6.334,6.02l-0.367,0.366                c-0.261,0.261-0.55,0.524-0.838,0.785c-3.376,3.167-7.25,5.706-11.385,7.538c-1.676,0.734-3.402,1.361-5.156,1.858                c-5.471,1.571-11.254,2.015-17.013,1.204l-0.314-0.053c-1.596-0.235-3.193-0.549-4.737-0.968                c-1.492-0.393-2.958-0.89-4.372-1.439c-2.408-0.915-4.711-2.068-6.909-3.429c-0.916-0.575-1.806-1.204-2.67-1.858                c-0.314-0.235-0.627-0.471-0.942-0.707l-10.26-8.113l-4.371-3.429l-7.486-5.916c-0.287-0.235-0.602-0.471-0.889-0.759                c-0.838-0.654-1.649-1.414-2.435-2.224c-0.366-0.314-0.733-0.707-1.072-1.074l-2.958-3.141l-4.71-4.973l-9.58-10.182                l-0.079-0.077l-0.392-0.42l-2.67-2.826l-2.277-2.435l-4.502-4.763l-3.717-3.926L2057.655,633.057z"/>
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000018925519392339339660000003935959899714437772_" gradientUnits="userSpaceOnUse" x1="-5154.1465" y1="703.7849" x2="-4978.1514" y2="703.7849" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
													<stop offset="0" style="stop-color:#FDFDFD"/>
													<stop offset="0.0027" style="stop-color:#F2F2F2"/>
													<stop offset="0.018" style="stop-color:#BABABA"/>
													<stop offset="0.0338" style="stop-color:#898989"/>
													<stop offset="0.0498" style="stop-color:#5F5F5F"/>
													<stop offset="0.0659" style="stop-color:#3C3C3C"/>
													<stop offset="0.0822" style="stop-color:#222222"/>
													<stop offset="0.0989" style="stop-color:#0F0F0F"/>
													<stop offset="0.116" style="stop-color:#040404"/>
													<stop offset="0.1344" style="stop-color:#000000"/>
													<stop offset="0.1553" style="stop-color:#090909"/>
													<stop offset="0.1899" style="stop-color:#222222"/>
													<stop offset="0.2339" style="stop-color:#4A4B4B"/>
													<stop offset="0.2855" style="stop-color:#828384"/>
													<stop offset="0.3429" style="stop-color:#C9CACB"/>
													<stop offset="0.3495" style="stop-color:#D1D3D4"/>
													<stop offset="0.4084" style="stop-color:#C8CACB"/>
													<stop offset="0.5058" style="stop-color:#B0B1B2"/>
													<stop offset="0.6294" style="stop-color:#888889"/>
													<stop offset="0.7733" style="stop-color:#525051"/>
													<stop offset="0.8226" style="stop-color:#3E3B3C"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												<path style="fill:url(#SVGID_00000018925519392339339660000003935959899714437772_);" d="M2057.655,633.057              c1.806,4.057,3.769,8.14,5.916,12.276c0.55,1.099,1.151,2.171,1.727,3.271c0.55,0.994,1.1,2.015,1.675,3.036              c0.733,1.308,1.491,2.617,2.251,3.926c4.135,7.04,8.794,14.108,13.977,21.148c0.654,0.916,1.361,1.832,2.068,2.775              c1.257,1.649,2.539,3.324,3.847,4.973l0.314,0.418h0.026c1.231,1.545,2.487,3.063,3.769,4.58              c1.727,2.094,3.507,4.162,5.366,6.23c2.329,2.643,4.763,5.234,7.25,7.826c0.758,0.785,1.517,1.57,2.329,2.356              c2.12,2.172,4.318,4.319,6.57,6.464c4.973,4.738,10.234,9.397,15.782,13.899c0.994,0.811,1.99,1.623,2.984,2.407              c7.224,5.759,14.919,11.282,23.085,16.568c6.936,4.502,14.239,8.847,21.882,12.93              c7.406,4.004,15.127,7.799,23.189,11.333c4.476,1.963,8.402,3.324,11.831,4.109              c27.822,6.595,22.352-23.033,10.286-53.63v-0.026c-11.621-29.393-29.262-59.729-29.262-59.729l-0.21,0.21              l-12.249,11.883l-4.162,4.056l-1.491,1.414v0.026l-0.261,0.262l-6.334,6.02l-0.367,0.366              c-0.261,0.261-0.55,0.524-0.838,0.785c-3.376,3.167-7.25,5.706-11.385,7.538c-1.676,0.734-3.402,1.361-5.156,1.858              c-5.471,1.571-11.254,2.015-17.013,1.204l-0.314-0.053c-1.596-0.235-3.193-0.549-4.737-0.968              c-1.492-0.393-2.958-0.89-4.372-1.439c-2.408-0.915-4.711-2.068-6.909-3.429c-0.916-0.575-1.806-1.204-2.67-1.858              c-0.314-0.235-0.627-0.471-0.942-0.707l-10.26-8.113l-4.371-3.429l-7.486-5.916c-0.287-0.235-0.602-0.471-0.889-0.759              c-0.838-0.654-1.649-1.414-2.435-2.224c-0.366-0.314-0.733-0.707-1.072-1.074l-2.958-3.141l-4.71-4.973l-9.58-10.182              l-0.079-0.077l-0.392-0.42l-2.67-2.826l-2.277-2.435l-4.502-4.763l-3.717-3.926L2057.655,633.057z"/>
											</g>
											<g>
												<path style="fill:#525252;" d="M2043.26,610.809l1.387,2.12l1.911,2.984l3.062,4.711l0.419,0.654l2.224,3.429              l5.392,8.349l0.052,0.052l3.115,4.816l3.796,5.863l1.91,2.984l1.57,2.408l2.644,4.109h0.027l1.492,2.33              c0.393,0.602,0.784,1.204,1.178,1.78c0,0.026,0.052,0.052,0.052,0.052c0.891,1.361,1.832,2.696,2.801,4.005              c1.021,1.387,2.069,2.748,3.141,4.083c0.942,1.178,1.911,2.303,2.879,3.429c0.105,0.157,0.236,0.288,0.367,0.418              c1.021,1.152,2.068,2.304,3.115,3.403c0.236,0.261,0.497,0.55,0.759,0.812c0.524,0.549,1.047,1.072,1.571,1.596              c0.55,0.523,1.125,1.073,1.674,1.596c1.099,1.047,2.199,2.067,3.351,3.063c1.125,1.02,2.277,1.989,3.454,2.931              c1.205,0.995,2.435,1.963,3.691,2.906c0.393,0.288,0.785,0.575,1.178,0.864c2.617,1.91,5.287,3.69,8.035,5.339              c0.917,0.576,1.832,1.126,2.775,1.649c0.025,0.026,0.078,0.052,0.104,0.052c0.968,0.55,1.911,1.072,2.879,1.57              c2.644,1.414,5.313,2.67,8.036,3.822c1.806,0.759,3.638,1.492,5.47,2.146c0.733,0.261,1.439,0.497,2.146,0.732              c0.785,0.262,1.545,0.524,2.303,0.76c0.733,0.209,1.466,0.419,2.198,0.628c0.733,0.21,1.466,0.419,2.172,0.603              c0.733,0.183,1.44,0.366,2.146,0.523c0.287,0.052,0.576,0.131,0.838,0.184c1.491,0.34,2.957,0.602,4.397,0.812              c1.23,0.183,2.434,0.34,3.612,0.445c0.733,0.078,1.439,0.13,2.146,0.157c0.968,0.052,1.911,0.079,2.827,0.079              c9.003,0,16.777-2.435,23.215-7.25c0.55-0.419,1.099-0.838,1.623-1.282c0.917-0.759,1.754-1.571,2.566-2.435              c0.392-0.393,0.785-0.811,1.177-1.23c0.55-0.628,1.047-1.257,1.544-1.911c0.261-0.34,0.524-0.68,0.759-1.021              c1.335-1.832,2.488-3.742,3.482-5.731c0.157-0.289,0.314-0.576,0.444-0.864c0.105-0.183,0.184-0.393,0.289-0.576v-0.026              c0-0.026,0-0.026,0-0.026c0.236-0.523,0.471-1.021,0.706-1.518c0.209-0.524,0.418-1.021,0.602-1.544              c0.209-0.524,0.393-1.021,0.576-1.545c0.183-0.497,0.367-1.02,0.523-1.517c2.068-6.335,2.932-12.538,3.245-17.17              c0-0.158,0.027-0.315,0.027-0.471c0.052-0.733,0.079-1.414,0.105-2.041c0.131-3.063,0.026-5.053,0-5.366              c-0.078-1.308-0.497-2.538-1.125-3.586c-1.466-2.408-4.188-3.953-7.171-3.769c-4.319,0.261-7.617,3.978-7.382,8.297              c0,0.236,0.026,0.471,0.026,0.733c0.027,0.288,0.027,0.602,0.027,0.943c0,0.575,0,1.23-0.027,1.91              c-0.026,0.549-0.052,1.1-0.078,1.701c-0.026,0.445-0.052,0.89-0.104,1.361c0,0.105-0.027,0.209-0.027,0.314              c-0.079,0.916-0.209,1.858-0.314,2.879c-0.027,0.262-0.079,0.524-0.131,0.785c-0.026,0.261-0.079,0.523-0.131,0.785              c-0.078,0.602-0.183,1.203-0.287,1.805c-0.027,0.106-0.052,0.21-0.079,0.315c-0.104,0.549-0.236,1.099-0.367,1.648              c-0.104,0.471-0.208,0.916-0.34,1.388c-0.236,0.916-0.498,1.832-0.812,2.748c-0.131,0.418-0.288,0.837-0.445,1.282              l-0.551,1.413c-0.366,0.891-0.758,1.78-1.204,2.644c-0.104,0.209-0.209,0.418-0.34,0.628              c-0.367,0.706-0.785,1.414-1.256,2.068c-0.236,0.367-0.471,0.733-0.733,1.073c-0.079,0.13-0.158,0.236-0.262,0.34              c-0.235,0.34-0.497,0.681-0.785,0.995c-0.34,0.392-0.68,0.758-1.047,1.125c-0.027,0.026-0.027,0.053-0.052,0.079              c-0.261,0.261-0.55,0.524-0.838,0.785c-0.419,0.366-0.837,0.707-1.283,1.047c-0.523,0.393-1.047,0.759-1.622,1.074              c-0.262,0.156-0.524,0.314-0.812,0.444c-0.287,0.183-0.602,0.34-0.916,0.445c-0.236,0.131-0.471,0.236-0.733,0.315              c-0.524,0.261-1.1,0.471-1.676,0.628c-0.052,0.026-0.104,0.051-0.156,0.051c-0.681,0.21-1.361,0.393-2.094,0.55              c-0.052,0-0.104,0.026-0.157,0.026c-0.628,0.13-1.309,0.235-1.99,0.314c-0.418,0.078-0.864,0.104-1.309,0.13              c-0.785,0.079-1.596,0.105-2.434,0.105c-1.57,0-3.22-0.131-4.947-0.34c-0.079,0-0.157,0-0.261-0.026              c0,0-0.027,0-0.052-0.026c-1.074-0.131-2.146-0.315-3.272-0.524c-0.262-0.052-0.55-0.105-0.838-0.158              c-1.413-0.314-2.854-0.654-4.371-1.099c-0.079-0.026-0.157-0.052-0.236-0.079c-0.68-0.183-1.388-0.392-2.094-0.628              c-0.445-0.13-0.89-0.288-1.335-0.445c-0.628-0.21-1.257-0.419-1.911-0.654c-1.178-0.42-2.355-0.864-3.507-1.335              c-0.759-0.314-1.518-0.628-2.278-0.995c-1.466-0.628-2.932-1.309-4.371-2.041c-0.969-0.471-1.937-0.995-2.879-1.545              c-0.392-0.183-0.785-0.418-1.151-0.628c-0.733-0.392-1.439-0.812-2.146-1.256c-1.989-1.204-3.952-2.487-5.862-3.848              c-0.655-0.445-1.282-0.916-1.938-1.414h-0.026c-0.21-0.157-0.418-0.314-0.628-0.471              c-0.104-0.079-0.183-0.157-0.288-0.236h-0.026c-0.969-0.733-1.937-1.492-2.853-2.251              c-0.105-0.104-0.21-0.183-0.314-0.288c-0.995-0.785-1.963-1.623-2.906-2.513c-0.157-0.131-0.315-0.262-0.446-0.419              c-0.025,0-0.025,0-0.025,0c-0.629-0.549-1.231-1.125-1.833-1.727c-0.026,0-0.026-0.026-0.052-0.026              c-0.026-0.027-0.078-0.079-0.105-0.105l-0.288-0.288c-0.733-0.681-1.44-1.413-2.146-2.12              c-0.366-0.367-0.707-0.733-1.047-1.126c-0.654-0.68-1.282-1.334-1.884-2.041c-0.131-0.131-0.262-0.261-0.393-0.419              c0,0,0,0,0-0.025c-0.498-0.55-0.969-1.1-1.44-1.65c-0.603-0.68-1.152-1.361-1.701-2.068              c-0.13-0.131-0.261-0.288-0.366-0.445c-0.523-0.654-1.048-1.335-1.571-2.015c-0.288-0.393-0.576-0.759-0.838-1.152              c-0.497-0.654-0.969-1.334-1.439-2.015c-0.654-0.916-1.282-1.859-1.911-2.827l-5.418-8.375l-0.026-0.026l-0.052-0.052              l-1.44-2.277l-0.184-0.288l-4.266-6.596l-1.256-1.911l-9.999-15.469l-5.601-8.637v-0.026l-0.759-1.152              c-0.131-0.209-0.288-0.419-0.445-0.602c0-0.027,0-0.027,0-0.027c-0.026-0.026-0.026-0.052-0.052-0.052              c-0.157-0.209-0.341-0.418-0.55-0.602c-0.577-0.576-1.231-1.047-1.937-1.414c-0.026-0.026-0.078-0.026-0.104-0.052              c-2.408-1.204-5.365-1.152-7.773,0.419C2041.951,602.302,2040.904,607.171,2043.26,610.809z"/>
											</g>
											<g style="opacity:0.4;">
												<g>
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000156587794239419122620000006981994549093632927_" filterUnits="userSpaceOnUse" x="2042.001" y="598.69" width="152.503" height="107.366">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2042.001" y="598.69" width="152.503" height="107.366" id="SVGID_00000016055389269036905480000008376447436787631248_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000156587794239419122620000006981994549093632927_);">
															<defs>
																
																	<filter id="Adobe_OpacityMaskFilter_00000042696602383532578030000002079715010400078525_" filterUnits="userSpaceOnUse" x="2042.001" y="598.69" width="152.503" height="107.366">
																	<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
																	<feBlend in="SourceGraphic" in2="back" mode="normal"/>
																</filter>
															</defs>
															
																<mask maskUnits="userSpaceOnUse" x="2042.001" y="598.69" width="152.503" height="107.366" id="SVGID_00000016055389269036905480000008376447436787631248_">
																<g style="filter:url(#Adobe_OpacityMaskFilter_00000042696602383532578030000002079715010400078525_);">
																</g>
															</mask>
															
																<linearGradient id="SVGID_00000059991780170154176490000001191711398829294006_" gradientUnits="userSpaceOnUse" x1="-5115" y1="652.3727" x2="-4962.4971" y2="652.3727" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
																<stop offset="0" style="stop-color:#FFFFFF"/>
																<stop offset="0.0074" style="stop-color:#EEEEEE"/>
																<stop offset="0.0273" style="stop-color:#C4C4C4"/>
																<stop offset="0.0477" style="stop-color:#A2A2A2"/>
																<stop offset="0.0683" style="stop-color:#888888"/>
																<stop offset="0.0894" style="stop-color:#757575"/>
																<stop offset="0.1111" style="stop-color:#6A6A6A"/>
																<stop offset="0.1344" style="stop-color:#666666"/>
																<stop offset="0.3495" style="stop-color:#666666"/>
																<stop offset="0.4821" style="stop-color:#5D5D5D"/>
																<stop offset="0.7004" style="stop-color:#444444"/>
																<stop offset="0.8226" style="stop-color:#333333"/>
																<stop offset="1" style="stop-color:#FFFFFF"/>
															</linearGradient>
															
																<path style="mask:url(#SVGID_00000016055389269036905480000008376447436787631248_);fill:url(#SVGID_00000059991780170154176490000001191711398829294006_);" d="                 M2043.26,610.809l1.387,2.12l1.911,2.984l3.062,4.711l0.419,0.654l2.224,3.429l5.392,8.349l0.052,0.052l3.115,4.816                 l3.796,5.863l1.91,2.984l1.57,2.408l2.644,4.109h0.027l1.492,2.33c0.393,0.602,0.784,1.204,1.178,1.78                 c0,0.026,0.052,0.052,0.052,0.052c0.891,1.361,1.832,2.696,2.801,4.005c1.021,1.387,2.069,2.748,3.141,4.083                 c0.942,1.178,1.911,2.303,2.879,3.429c0.105,0.157,0.236,0.288,0.367,0.418c1.021,1.152,2.068,2.304,3.115,3.403                 c0.236,0.261,0.497,0.55,0.759,0.812c0.524,0.549,1.047,1.072,1.571,1.596c0.55,0.523,1.125,1.073,1.674,1.596                 c1.099,1.047,2.199,2.067,3.351,3.063c1.125,1.02,2.277,1.989,3.454,2.931c1.205,0.995,2.435,1.963,3.691,2.906                 c0.393,0.288,0.785,0.575,1.178,0.838v0.026c2.617,1.91,5.287,3.69,8.035,5.339c0.917,0.576,1.832,1.126,2.775,1.649                 c0.025,0.026,0.078,0.052,0.104,0.052c0.968,0.55,1.911,1.072,2.879,1.57c2.644,1.414,5.313,2.67,8.036,3.822                 c1.806,0.759,3.638,1.492,5.47,2.146c0.733,0.261,1.439,0.497,2.146,0.732c0.785,0.262,1.545,0.524,2.303,0.76                 c0.733,0.209,1.466,0.419,2.198,0.628c0.733,0.21,1.466,0.419,2.172,0.603c0.733,0.183,1.44,0.366,2.146,0.523                 c0.287,0.052,0.576,0.131,0.838,0.184c1.491,0.34,2.957,0.602,4.397,0.812c1.23,0.183,2.434,0.34,3.612,0.445                 c0.733,0.078,1.439,0.13,2.146,0.157c0.968,0.052,1.911,0.079,2.827,0.079c9.003,0,16.777-2.435,23.215-7.25                 c0.55-0.419,1.099-0.838,1.623-1.282c0.917-0.759,1.754-1.571,2.566-2.435c0.392-0.393,0.785-0.811,1.177-1.23                 c0.55-0.628,1.047-1.257,1.544-1.911c0.261-0.34,0.524-0.68,0.759-1.021c1.335-1.832,2.488-3.742,3.482-5.731                 c0.157-0.289,0.314-0.576,0.444-0.864c0.105-0.183,0.184-0.393,0.289-0.576v-0.026c0-0.026,0-0.026,0-0.026                 c0.236-0.523,0.471-1.021,0.706-1.518c0.209-0.524,0.418-1.021,0.602-1.544c0.209-0.524,0.393-1.021,0.576-1.545                 c0.183-0.497,0.367-1.02,0.523-1.517c2.068-6.335,2.932-12.538,3.245-17.17c0-0.158,0.027-0.315,0.027-0.471                 c0.052-0.733,0.079-1.414,0.105-2.041c0.131-3.063,0.026-5.053,0-5.366c-0.078-1.308-0.497-2.538-1.125-3.586                 c-1.466-2.408-4.188-3.953-7.171-3.769c-4.319,0.261-7.617,3.978-7.382,8.297c0,0.236,0.026,0.471,0.026,0.733                 c0.027,0.288,0.027,0.602,0.027,0.943c0,0.575,0,1.23-0.027,1.91c-0.026,0.549-0.052,1.1-0.078,1.701                 c-0.026,0.445-0.052,0.89-0.104,1.361c0,0.105-0.027,0.209-0.027,0.314c-0.079,0.916-0.209,1.858-0.314,2.879                 c-0.027,0.262-0.079,0.524-0.131,0.785c-0.026,0.261-0.052,0.523-0.105,0.785c0,0,0,0,0,0.026                 c-0.104,0.576-0.209,1.178-0.314,1.779c-0.027,0.106-0.052,0.21-0.079,0.315c-0.104,0.549-0.236,1.099-0.367,1.648                 c-0.104,0.471-0.208,0.916-0.34,1.388c-0.236,0.916-0.498,1.832-0.812,2.748c-0.131,0.418-0.288,0.837-0.445,1.282                 l-0.551,1.413c-0.366,0.891-0.758,1.78-1.204,2.644c-0.104,0.209-0.209,0.418-0.34,0.628                 c-0.367,0.706-0.785,1.414-1.256,2.068c-0.236,0.367-0.471,0.733-0.733,1.073c-0.079,0.13-0.158,0.236-0.262,0.34                 c-0.235,0.34-0.497,0.681-0.785,0.995c-0.34,0.392-0.68,0.758-1.047,1.125c-0.027,0.026-0.027,0.053-0.052,0.079                 c-0.261,0.261-0.55,0.524-0.838,0.785c-0.419,0.366-0.837,0.707-1.283,1.047c-0.523,0.393-1.047,0.759-1.622,1.074                 c-0.262,0.156-0.524,0.314-0.812,0.444c-0.287,0.183-0.602,0.34-0.916,0.445c-0.236,0.131-0.471,0.236-0.733,0.315                 c-0.524,0.261-1.1,0.471-1.676,0.628c-0.052,0.026-0.104,0.051-0.156,0.051c-0.681,0.21-1.361,0.393-2.094,0.55                 c-0.052,0-0.104,0.026-0.157,0.026c-0.628,0.13-1.309,0.235-1.99,0.314c-0.418,0.078-0.864,0.104-1.309,0.13                 c-0.785,0.079-1.596,0.105-2.434,0.105c-1.57,0-3.22-0.131-4.947-0.34c-0.079,0-0.157,0-0.261-0.026                 c0,0-0.027,0-0.052-0.026c-1.074-0.131-2.146-0.315-3.272-0.524c-0.262-0.052-0.55-0.105-0.838-0.158                 c-1.413-0.314-2.854-0.654-4.371-1.099c-0.079-0.026-0.157-0.052-0.236-0.079c-0.68-0.183-1.388-0.392-2.094-0.628                 c-0.445-0.13-0.89-0.288-1.335-0.445c-0.628-0.21-1.257-0.419-1.911-0.654c-1.178-0.42-2.355-0.864-3.507-1.335                 c-0.759-0.314-1.518-0.628-2.278-0.995c-1.466-0.628-2.932-1.309-4.371-2.041c-0.969-0.471-1.937-0.995-2.879-1.545                 c-0.392-0.183-0.785-0.418-1.151-0.628c-0.733-0.392-1.439-0.812-2.146-1.256c-1.989-1.204-3.952-2.487-5.862-3.848                 c-0.655-0.445-1.282-0.916-1.938-1.414h-0.026c-0.21-0.157-0.418-0.314-0.628-0.471                 c-0.104-0.079-0.183-0.157-0.288-0.236h-0.026c-0.969-0.733-1.937-1.492-2.853-2.251                 c-0.105-0.104-0.21-0.183-0.314-0.288c-0.995-0.811-1.963-1.648-2.906-2.513c-0.157-0.131-0.315-0.262-0.446-0.419                 c-0.025,0-0.025,0-0.025,0c-0.629-0.549-1.231-1.125-1.833-1.727c-0.026,0-0.026-0.026-0.052-0.026                 c-0.026-0.027-0.078-0.079-0.105-0.105l-0.288-0.288c-0.733-0.681-1.44-1.413-2.146-2.12                 c-0.366-0.367-0.707-0.733-1.047-1.126c-0.654-0.68-1.282-1.334-1.884-2.041c-0.131-0.131-0.262-0.261-0.393-0.419                 c0,0,0,0,0-0.025c-0.498-0.55-0.969-1.1-1.44-1.65c-0.603-0.68-1.152-1.361-1.701-2.068                 c-0.13-0.131-0.261-0.288-0.366-0.445c-0.523-0.654-1.048-1.335-1.571-2.015c-0.288-0.393-0.576-0.759-0.838-1.152                 c-0.497-0.654-0.969-1.334-1.439-2.015c-0.654-0.916-1.282-1.859-1.911-2.827l-5.418-8.375l-0.026-0.026                 l-0.052-0.052l-1.44-2.277l-0.184-0.288l-4.266-6.596l-1.256-1.911l-9.999-15.469l-5.601-8.637v-0.026l-0.759-1.152                 c-0.131-0.209-0.288-0.419-0.445-0.602c0-0.027,0-0.027,0-0.027c-0.026-0.026-0.026-0.052-0.052-0.052                 c-0.157-0.209-0.341-0.418-0.55-0.602c-0.577-0.576-1.231-1.047-1.937-1.414c-0.026-0.026-0.078-0.026-0.104-0.052                 c-2.408-1.204-5.365-1.152-7.773,0.419C2041.951,602.302,2040.904,607.171,2043.26,610.809z"/>
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000018941521901411896120000008618970324490285458_" gradientUnits="userSpaceOnUse" x1="-5115" y1="652.3727" x2="-4962.4971" y2="652.3727" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
														<stop offset="0" style="stop-color:#FDFDFD"/>
														<stop offset="0.0027" style="stop-color:#F2F2F2"/>
														<stop offset="0.018" style="stop-color:#BABABA"/>
														<stop offset="0.0338" style="stop-color:#898989"/>
														<stop offset="0.0498" style="stop-color:#5F5F5F"/>
														<stop offset="0.0659" style="stop-color:#3C3C3C"/>
														<stop offset="0.0822" style="stop-color:#222222"/>
														<stop offset="0.0989" style="stop-color:#0F0F0F"/>
														<stop offset="0.116" style="stop-color:#040404"/>
														<stop offset="0.1344" style="stop-color:#000000"/>
														<stop offset="0.1553" style="stop-color:#090909"/>
														<stop offset="0.1899" style="stop-color:#222222"/>
														<stop offset="0.2339" style="stop-color:#4A4B4B"/>
														<stop offset="0.2855" style="stop-color:#828384"/>
														<stop offset="0.3429" style="stop-color:#C9CACB"/>
														<stop offset="0.3495" style="stop-color:#D1D3D4"/>
														<stop offset="0.4084" style="stop-color:#C8CACB"/>
														<stop offset="0.5058" style="stop-color:#B0B1B2"/>
														<stop offset="0.6294" style="stop-color:#888889"/>
														<stop offset="0.7733" style="stop-color:#525051"/>
														<stop offset="0.8226" style="stop-color:#3E3B3C"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													<path style="fill:url(#SVGID_00000018941521901411896120000008618970324490285458_);" d="M2043.26,610.809l1.387,2.12               l1.911,2.984l3.062,4.711l0.419,0.654l2.224,3.429l5.392,8.349l0.052,0.052l3.115,4.816l3.796,5.863l1.91,2.984               l1.57,2.408l2.644,4.109h0.027l1.492,2.33c0.393,0.602,0.784,1.204,1.178,1.78c0,0.026,0.052,0.052,0.052,0.052               c0.891,1.361,1.832,2.696,2.801,4.005c1.021,1.387,2.069,2.748,3.141,4.083c0.942,1.178,1.911,2.303,2.879,3.429               c0.105,0.157,0.236,0.288,0.367,0.418c1.021,1.152,2.068,2.304,3.115,3.403c0.236,0.261,0.497,0.55,0.759,0.812               c0.524,0.549,1.047,1.072,1.571,1.596c0.55,0.523,1.125,1.073,1.674,1.596c1.099,1.047,2.199,2.067,3.351,3.063               c1.125,1.02,2.277,1.989,3.454,2.931c1.205,0.995,2.435,1.963,3.691,2.906c0.393,0.288,0.785,0.575,1.178,0.838v0.026               c2.617,1.91,5.287,3.69,8.035,5.339c0.917,0.576,1.832,1.126,2.775,1.649c0.025,0.026,0.078,0.052,0.104,0.052               c0.968,0.55,1.911,1.072,2.879,1.57c2.644,1.414,5.313,2.67,8.036,3.822c1.806,0.759,3.638,1.492,5.47,2.146               c0.733,0.261,1.439,0.497,2.146,0.732c0.785,0.262,1.545,0.524,2.303,0.76c0.733,0.209,1.466,0.419,2.198,0.628               c0.733,0.21,1.466,0.419,2.172,0.603c0.733,0.183,1.44,0.366,2.146,0.523c0.287,0.052,0.576,0.131,0.838,0.184               c1.491,0.34,2.957,0.602,4.397,0.812c1.23,0.183,2.434,0.34,3.612,0.445c0.733,0.078,1.439,0.13,2.146,0.157               c0.968,0.052,1.911,0.079,2.827,0.079c9.003,0,16.777-2.435,23.215-7.25c0.55-0.419,1.099-0.838,1.623-1.282               c0.917-0.759,1.754-1.571,2.566-2.435c0.392-0.393,0.785-0.811,1.177-1.23c0.55-0.628,1.047-1.257,1.544-1.911               c0.261-0.34,0.524-0.68,0.759-1.021c1.335-1.832,2.488-3.742,3.482-5.731c0.157-0.289,0.314-0.576,0.444-0.864               c0.105-0.183,0.184-0.393,0.289-0.576v-0.026c0-0.026,0-0.026,0-0.026c0.236-0.523,0.471-1.021,0.706-1.518               c0.209-0.524,0.418-1.021,0.602-1.544c0.209-0.524,0.393-1.021,0.576-1.545c0.183-0.497,0.367-1.02,0.523-1.517               c2.068-6.335,2.932-12.538,3.245-17.17c0-0.158,0.027-0.315,0.027-0.471c0.052-0.733,0.079-1.414,0.105-2.041               c0.131-3.063,0.026-5.053,0-5.366c-0.078-1.308-0.497-2.538-1.125-3.586c-1.466-2.408-4.188-3.953-7.171-3.769               c-4.319,0.261-7.617,3.978-7.382,8.297c0,0.236,0.026,0.471,0.026,0.733c0.027,0.288,0.027,0.602,0.027,0.943               c0,0.575,0,1.23-0.027,1.91c-0.026,0.549-0.052,1.1-0.078,1.701c-0.026,0.445-0.052,0.89-0.104,1.361               c0,0.105-0.027,0.209-0.027,0.314c-0.079,0.916-0.209,1.858-0.314,2.879c-0.027,0.262-0.079,0.524-0.131,0.785               c-0.026,0.261-0.052,0.523-0.105,0.785c0,0,0,0,0,0.026c-0.104,0.576-0.209,1.178-0.314,1.779               c-0.027,0.106-0.052,0.21-0.079,0.315c-0.104,0.549-0.236,1.099-0.367,1.648c-0.104,0.471-0.208,0.916-0.34,1.388               c-0.236,0.916-0.498,1.832-0.812,2.748c-0.131,0.418-0.288,0.837-0.445,1.282l-0.551,1.413               c-0.366,0.891-0.758,1.78-1.204,2.644c-0.104,0.209-0.209,0.418-0.34,0.628c-0.367,0.706-0.785,1.414-1.256,2.068               c-0.236,0.367-0.471,0.733-0.733,1.073c-0.079,0.13-0.158,0.236-0.262,0.34c-0.235,0.34-0.497,0.681-0.785,0.995               c-0.34,0.392-0.68,0.758-1.047,1.125c-0.027,0.026-0.027,0.053-0.052,0.079c-0.261,0.261-0.55,0.524-0.838,0.785               c-0.419,0.366-0.837,0.707-1.283,1.047c-0.523,0.393-1.047,0.759-1.622,1.074c-0.262,0.156-0.524,0.314-0.812,0.444               c-0.287,0.183-0.602,0.34-0.916,0.445c-0.236,0.131-0.471,0.236-0.733,0.315c-0.524,0.261-1.1,0.471-1.676,0.628               c-0.052,0.026-0.104,0.051-0.156,0.051c-0.681,0.21-1.361,0.393-2.094,0.55c-0.052,0-0.104,0.026-0.157,0.026               c-0.628,0.13-1.309,0.235-1.99,0.314c-0.418,0.078-0.864,0.104-1.309,0.13c-0.785,0.079-1.596,0.105-2.434,0.105               c-1.57,0-3.22-0.131-4.947-0.34c-0.079,0-0.157,0-0.261-0.026c0,0-0.027,0-0.052-0.026               c-1.074-0.131-2.146-0.315-3.272-0.524c-0.262-0.052-0.55-0.105-0.838-0.158c-1.413-0.314-2.854-0.654-4.371-1.099               c-0.079-0.026-0.157-0.052-0.236-0.079c-0.68-0.183-1.388-0.392-2.094-0.628c-0.445-0.13-0.89-0.288-1.335-0.445               c-0.628-0.21-1.257-0.419-1.911-0.654c-1.178-0.42-2.355-0.864-3.507-1.335c-0.759-0.314-1.518-0.628-2.278-0.995               c-1.466-0.628-2.932-1.309-4.371-2.041c-0.969-0.471-1.937-0.995-2.879-1.545c-0.392-0.183-0.785-0.418-1.151-0.628               c-0.733-0.392-1.439-0.812-2.146-1.256c-1.989-1.204-3.952-2.487-5.862-3.848c-0.655-0.445-1.282-0.916-1.938-1.414               h-0.026c-0.21-0.157-0.418-0.314-0.628-0.471c-0.104-0.079-0.183-0.157-0.288-0.236h-0.026               c-0.969-0.733-1.937-1.492-2.853-2.251c-0.105-0.104-0.21-0.183-0.314-0.288c-0.995-0.811-1.963-1.648-2.906-2.513               c-0.157-0.131-0.315-0.262-0.446-0.419c-0.025,0-0.025,0-0.025,0c-0.629-0.549-1.231-1.125-1.833-1.727               c-0.026,0-0.026-0.026-0.052-0.026c-0.026-0.027-0.078-0.079-0.105-0.105l-0.288-0.288               c-0.733-0.681-1.44-1.413-2.146-2.12c-0.366-0.367-0.707-0.733-1.047-1.126c-0.654-0.68-1.282-1.334-1.884-2.041               c-0.131-0.131-0.262-0.261-0.393-0.419c0,0,0,0,0-0.025c-0.498-0.55-0.969-1.1-1.44-1.65               c-0.603-0.68-1.152-1.361-1.701-2.068c-0.13-0.131-0.261-0.288-0.366-0.445c-0.523-0.654-1.048-1.335-1.571-2.015               c-0.288-0.393-0.576-0.759-0.838-1.152c-0.497-0.654-0.969-1.334-1.439-2.015c-0.654-0.916-1.282-1.859-1.911-2.827               l-5.418-8.375l-0.026-0.026l-0.052-0.052l-1.44-2.277l-0.184-0.288l-4.266-6.596l-1.256-1.911l-9.999-15.469               l-5.601-8.637v-0.026l-0.759-1.152c-0.131-0.209-0.288-0.419-0.445-0.602c0-0.027,0-0.027,0-0.027               c-0.026-0.026-0.026-0.052-0.052-0.052c-0.157-0.209-0.341-0.418-0.55-0.602c-0.577-0.576-1.231-1.047-1.937-1.414               c-0.026-0.026-0.078-0.026-0.104-0.052c-2.408-1.204-5.365-1.152-7.773,0.419               C2041.951,602.302,2040.904,607.171,2043.26,610.809z"/>
												</g>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M2129.948,692.628c0,3.22,0.34,6.308,0.968,9.239c8.245,2.801,15.809,4.188,22.641,4.188              c9.003,0,16.777-2.435,23.215-7.25c0.55-0.419,1.099-0.838,1.623-1.282c0.184-1.597,0.262-3.245,0.262-4.895              c0-6.282-1.282-12.144-3.534-17.144c-1.727,4.241-4.24,8.14-7.8,10.784c-7.223,5.392-18.425,5.444-33.214,0.105              c-1.178-0.42-2.355-0.864-3.507-1.335C2130.156,687.472,2129.948,690.011,2129.948,692.628z"/>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M2060.823,637.925l7.276,11.255l4.162,6.439c4.188,6.491,8.978,12.511,14.291,17.982              c1.047,1.099,2.147,2.146,3.246,3.192c1.099,1.047,2.199,2.067,3.351,3.063c1.125,1.02,2.277,1.989,3.454,2.931              c0.995-1.334,1.886-2.8,2.67-4.371c0.446-0.837,0.837-1.701,1.204-2.591c1.099-2.565,1.911-5.34,2.46-8.271              c-0.837-0.733-1.649-1.492-2.46-2.277l-0.288-0.288c-0.733-0.681-1.44-1.413-2.146-2.12              c-1.649-1.701-3.246-3.455-4.764-5.261c-0.603-0.68-1.152-1.361-1.701-2.068c-0.13-0.131-0.261-0.288-0.366-0.445              c-0.523-0.654-1.048-1.335-1.571-2.015c-0.288-0.393-0.576-0.759-0.838-1.152c-0.497-0.654-0.969-1.334-1.439-2.015              c-0.654-0.916-1.282-1.859-1.911-2.827l-5.418-8.375l-0.026-0.026l-1.492-2.329l-5.706-8.795              c-1.204,0.445-2.382,1.021-3.508,1.727c-2.224,1.335-4.266,3.141-6.073,5.314c-0.654,0.759-1.256,1.57-1.805,2.434              C2061.215,637.323,2061.006,637.611,2060.823,637.925z"/>
											</g>
											<g style="opacity:0.15;">
												<path style="fill:#FFFCFF;" d="M2043.26,610.809l1.387,2.12l1.911,2.984l3.062,4.711l0.419,0.654l2.224,3.429              l5.392,8.349l0.052,0.052l3.115,4.816l3.796,5.863l1.91,2.984l1.57,2.408c0.969-0.628,1.884-1.335,2.775-2.121              c0,0,0.026-0.026,0.052-0.052c0.445-0.392,0.89-0.811,1.309-1.23c2.355-2.381,4.397-5.365,5.967-8.768              c0.105-0.209,0.209-0.445,0.314-0.654l-0.184-0.288l-4.266-6.596l-1.256-1.911l-9.999-15.469l-5.601-8.637v-0.026              l-0.759-1.152c-0.131-0.209-0.288-0.419-0.445-0.602c0-0.027,0-0.027,0-0.027c-0.026-0.026-0.026-0.052-0.052-0.052              c-0.157-0.209-0.341-0.418-0.55-0.602c-0.577-0.576-1.231-1.047-1.937-1.414c-0.026-0.026-0.078-0.026-0.104-0.052              c-2.408-1.204-5.365-1.152-7.773,0.419C2041.951,602.302,2040.904,607.171,2043.26,610.809z"/>
											</g>
											<g style="opacity:0.04;">
												<path style="fill:#FFFCFF;" d="M2050.039,621.279l2.224,3.429l5.392,8.349c1.806,4.057,3.769,8.14,5.916,12.276              c0.55,1.099,1.151,2.171,1.727,3.271c0.55,0.994,1.1,2.015,1.675,3.036c0.733,1.308,1.491,2.617,2.251,3.926              c4.135,7.04,8.794,14.108,13.977,21.148c0.654,0.916,1.361,1.832,2.068,2.775c1.257,1.649,2.539,3.324,3.847,4.973              l0.314,0.418h0.026l3.324,4.397l5.601,7.329l6.438,8.427l2.094,2.749l5.471,7.172c0.837-0.837,1.623-1.701,2.356-2.618              c2.669-3.141,5.025-6.621,7.04-10.417c0.079-0.105,0.131-0.209,0.183-0.314c0.366-0.654,0.733-1.361,1.073-2.068              c0.079-0.184,0.183-0.366,0.262-0.55c0.628-1.335,1.23-2.696,1.754-4.109c0.209-0.497,0.392-0.994,0.575-1.518              c1.1-2.984,1.99-6.099,2.696-9.318c0.184-0.837,0.34-1.675,0.471-2.538c0.184-0.891,0.314-1.807,0.445-2.722              c0.261-1.833,0.445-3.717,0.55-5.601c0.027-0.419,0.052-0.812,0.079-1.23c0.052-1.152,0.079-2.304,0.079-3.455              c0-0.471,0-0.942-0.026-1.413c0-0.445-0.026-0.889-0.026-1.334c-3.718-0.812-7.513-1.885-11.412-3.246              c-3.874-1.361-7.852-3.036-11.857-5.052c-0.602-0.314-1.23-0.628-1.832-0.942c-1.047-0.55-2.121-1.125-3.193-1.754              c-0.943-0.523-1.858-1.073-2.801-1.648c-0.603-0.367-1.204-0.76-1.833-1.151c-0.811-0.498-1.623-1.048-2.434-1.597              c-0.785-0.524-1.597-1.099-2.409-1.675c-1.622-1.152-3.245-2.382-4.867-3.691c-2.408-1.937-4.817-4.031-7.225-6.281              l-0.026-0.026l-0.052-0.052l-1.44-2.277l-0.184-0.288l-4.266-6.596l-1.256-1.911l-9.999-15.469              c-2.486,1.205-4.842,2.618-7.093,4.293c-1.859,1.36-3.664,2.878-5.34,4.58              C2050.274,621.044,2050.143,621.174,2050.039,621.279z"/>
											</g>
											<g style="opacity:0.04;">
												<path style="fill:#FFFCFF;" d="M2098.382,696.607l6.438,8.427l2.094,2.749l5.471,7.172l3.847,5.052l3.376,4.423              l13.114,17.17c1.387,1.832,2.826,3.664,4.292,5.443c1.204,1.492,2.46,2.985,3.717,4.45              c0.209,0.21,0.393,0.446,0.602,0.682c0.969,1.099,1.963,2.224,2.984,3.297c0.472,0.524,0.942,1.047,1.44,1.57              c1.309,1.388,2.617,2.749,3.978,4.084c0.419,0.418,0.812,0.837,1.23,1.23c0.943,0.915,1.885,1.832,2.854,2.722              c2.277,2.094,4.449,4.031,6.569,5.785c6.78-2.617,12.904-7.015,18.087-12.72c5.13-5.732,9.291-12.773,12.118-20.703              c2.381-6.701,3.847-14.029,4.083-21.725c0.053-0.916,0.078-1.805,0.078-2.721c0-10.601-2.198-20.573-6.097-29.236              v-0.026c0-0.026,0-0.026,0-0.026c-1.833-4.135-4.057-7.957-6.596-11.411c-1.309-1.754-2.67-3.402-4.11-4.974              c-0.104-0.105-0.183-0.208-0.314-0.314c-0.498-0.55-1.021-1.073-1.545-1.57c-0.287,0.079-0.628,0.183-0.994,0.262              c-1.179,0.287-2.722,0.628-4.555,0.968c-2.513,0.47-5.627,0.916-9.186,1.204c-0.943,0.079-1.886,0.158-2.906,0.21              c-1.963,0.131-4.057,0.183-6.282,0.183c-5.444-0.027-11.517-0.471-18.034-1.649c-1.125-0.209-2.276-0.445-3.455-0.707              c-0.261-0.026-0.524-0.104-0.784-0.156c-3.718-0.812-7.513-1.885-11.412-3.246c-2.408,1.963-4.686,4.214-6.752,6.674              c-0.419,0.471-0.812,0.969-1.205,1.466c-0.523,0.654-0.994,1.309-1.492,1.963h-0.026              c-0.052,0.079-0.104,0.157-0.157,0.236c-1.44,2.016-2.802,4.161-4.005,6.413c-0.366,0.654-0.707,1.335-1.047,2.015              c-0.837,1.701-1.623,3.455-2.329,5.261v0.026c-0.418,1.02-0.785,2.015-1.126,3.062              c-0.078,0.209-0.157,0.393-0.209,0.602c-0.21,0.654-0.418,1.309-0.628,1.989c-0.34,1.152-0.628,2.303-0.916,3.482              C2098.513,696.005,2098.434,696.293,2098.382,696.607z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M2149.734,761.125c0.419,0.418,0.812,0.837,1.23,1.23              c0.943,0.915,1.885,1.832,2.854,2.722c2.277,2.094,4.449,4.031,6.569,5.785c31.017,25.807,45.988,15.809,53.107,2.722              c27.822,6.595,22.352-23.033,10.286-53.63v-0.026c-6.438-3.22-13.453-4.973-20.781-4.973              c-2.827,0-5.601,0.261-8.323,0.759c-8.611,1.597-16.594,5.654-23.503,11.596c-5.654,4.816-10.575,10.888-14.58,17.902              C2153.818,750.079,2151.515,755.419,2149.734,761.125z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M2178.709,658.76c-0.026,0.445-0.052,0.89-0.104,1.361              c5.392-3.193,10.339-6.936,14.71-11.176c-1.466-2.408-4.188-3.953-7.171-3.769c-4.319,0.261-7.617,3.978-7.382,8.297              c0.052,0.969,0.079,2.172,0.026,3.586C2178.761,657.608,2178.735,658.158,2178.709,658.76z"/>
											</g>
											<g style="opacity:0.07;">
												<path style="fill:#1C1C1C;" d="M2053.363,599.528c0.079,5.811,0.89,11.438,2.356,16.856              c1.309,4.711,3.088,9.265,5.339,13.558c8.114,15.651,22.195,28.293,39.575,35.465c3.56,1.519,7.276,2.775,11.098,3.769              c5.783,1.545,11.882,2.486,18.138,2.775c1.465,0.105,2.958,0.131,4.449,0.131c9.476,0,18.583-1.492,27.038-4.215              c-8.35,0.733-19.291,0.628-31.461-2.119c-3.718-0.812-7.513-1.885-11.412-3.246              c-12.433-4.398-25.651-11.752-38.45-23.791l-0.026-0.026l-0.052-0.052l-1.44-2.277l-5.706-8.795l-9.999-15.469              l-6.36-9.815C2055.666,601.047,2054.593,600.131,2053.363,599.528z"/>
											</g>
										</g>
									</g>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000146468788492421437910000012891907601045183105_" filterUnits="userSpaceOnUse" x="2022.879" y="572.007" width="229.893" height="241.601">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2022.879" y="572.007" width="229.893" height="241.601" id="SVGID_00000021828511133030372370000000809282639204517281_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000146468788492421437910000012891907601045183105_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000009579872949449898730000009883617878131491489_" filterUnits="userSpaceOnUse" x="2022.879" y="572.007" width="229.893" height="241.601">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2022.879" y="572.007" width="229.893" height="241.601" id="SVGID_00000021828511133030372370000000809282639204517281_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000009579872949449898730000009883617878131491489_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000093178052110375884660000000325649859413954736_" gradientUnits="userSpaceOnUse" x1="-5058.3218" y1="813.6085" x2="-5058.3218" y2="572.0073" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000021828511133030372370000000809282639204517281_);fill:url(#SVGID_00000093178052110375884660000000325649859413954736_);" d="             M2023.123,592.271c0.079,0.418,0.158,0.864,0.236,1.309c1.256,6.674,3.952,15.259,8.846,26.252             c2.094,4.737,4.607,9.92,7.617,15.599c2.539,4.79,5.391,9.946,8.663,15.469c8.69,14.815,20.102,32.298,34.994,52.976             c3.246,4.58,6.727,9.317,10.365,14.212c1.727,2.33,3.455,4.607,5.209,6.858c74.7,97.052,143.588,110.532,152.697,59.388             c3.089-17.301-0.681-42.009-13.375-73.392c-3.822-9.423-8.244-18.243-13.165-26.462c-0.47-0.785-0.942-1.57-1.466-2.355             c-4.764-7.773-9.972-14.997-15.521-21.724c-16.228-19.735-35.413-35.204-55.069-47.348             c-27.875-17.222-56.744-27.692-79.595-33.973c-12.04-3.324-22.431-5.471-30.126-6.805             C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000019669088900520099660000008674359512900745391_" gradientUnits="userSpaceOnUse" x1="-5058.3218" y1="813.6085" x2="-5058.3218" y2="572.0073" gradientTransform="matrix(-1 0 0 1 -2920.4963 0)">
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="opacity:0.8;fill:url(#SVGID_00000019669088900520099660000008674359512900745391_);" d="M2023.123,592.271           c0.079,0.418,0.158,0.864,0.236,1.309c1.256,6.674,3.952,15.259,8.846,26.252c2.094,4.737,4.607,9.92,7.617,15.599           c2.539,4.79,5.391,9.946,8.663,15.469c8.69,14.815,20.102,32.298,34.994,52.976c3.246,4.58,6.727,9.317,10.365,14.212           c1.727,2.33,3.455,4.607,5.209,6.858c74.7,97.052,143.588,110.532,152.697,59.388c3.089-17.301-0.681-42.009-13.375-73.392           c-3.822-9.423-8.244-18.243-13.165-26.462c-0.47-0.785-0.942-1.57-1.466-2.355c-4.764-7.773-9.972-14.997-15.521-21.724           c-16.228-19.735-35.413-35.204-55.069-47.348c-27.875-17.222-56.744-27.692-79.595-33.973           c-12.04-3.324-22.431-5.471-30.126-6.805C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
									<path style="opacity:0.03;fill:#F2F8FC;" d="M2099.054,724.946c74.7,97.052,143.588,110.532,152.697,59.388           c3.089-17.301-0.681-42.009-13.375-73.392c-3.822-9.423-8.244-18.243-13.165-26.462           c-5.837,0.026-11.595,0.262-17.248,0.733c-6.046,0.471-12.014,1.151-17.851,2.068c-1.23,0.184-2.46,0.393-3.69,0.628           c-2.67,0.446-5.287,0.942-7.904,1.492c-2.12,0.444-4.214,0.916-6.308,1.438c-5.758,1.361-11.385,2.932-16.855,4.738           c-1.727,0.549-3.403,1.125-5.078,1.728c-3.769,1.335-7.459,2.774-11.071,4.318c-1.257,0.524-2.513,1.047-3.743,1.624           c-0.026,0-0.026,0.026-0.026,0.026c-0.628,0.261-1.257,0.549-1.885,0.837c-1.073,0.498-2.146,0.994-3.219,1.518           c-0.105,0.052-0.236,0.105-0.34,0.157c-0.759,0.366-1.492,0.733-2.251,1.125c-4.083,2.042-8.036,4.213-11.831,6.518           c-1.099,0.628-2.146,1.282-3.22,1.936C2107.926,718.376,2103.372,721.57,2099.054,724.946z"/>
									<path style="opacity:0.03;fill:#F2F8FC;" d="M2083.48,703.877c3.246,4.58,6.727,9.317,10.365,14.212           c1.727,2.33,3.455,4.607,5.209,6.858c74.7,97.052,143.588,110.532,152.697,59.388           c-4.764-8.847-10.863-17.197-18.139-24.943c-11.019-11.805-24.76-22.196-40.543-30.702           c-4.58-2.461-9.344-4.763-14.265-6.91c-10.443-4.554-21.619-8.323-33.345-11.255c-2.669-0.628-5.34-1.256-8.061-1.805           c-0.994-0.21-1.963-0.419-2.958-0.603c-2.225-0.444-4.45-0.837-6.701-1.203c-2.723-0.445-5.444-0.837-8.192-1.178           c-3.481-0.471-7.015-0.837-10.574-1.125c-0.733-0.053-1.492-0.131-2.225-0.183c-0.47-0.027-0.968-0.053-1.465-0.079           c-0.34-0.027-0.681-0.052-1.048-0.079c-4.973-0.314-10.024-0.471-15.128-0.471           C2087.222,703.798,2085.338,703.823,2083.48,703.877z"/>
									<path style="opacity:0.09;fill:#F2F8FC;" d="M2023.123,592.271c0.079,0.418,0.158,0.864,0.236,1.309           c1.256,6.674,3.952,15.259,8.846,26.252c2.094,4.737,4.607,9.92,7.617,15.599c3.717,0.21,7.485,0.315,11.28,0.341           c0.733,0.026,1.492,0.026,2.252,0.026c1.858,0,3.689-0.026,5.522-0.078h0.524c0.261,0,0.497,0,0.759-0.026           c0.759,0,1.491-0.026,2.224-0.052c0.471-0.026,0.916-0.052,1.361-0.079c0.183,0,0.367,0,0.55-0.026           c2.015-0.079,4.005-0.183,5.968-0.34c1.387-0.079,2.775-0.183,4.135-0.315c0.34-0.026,0.681-0.052,1.021-0.078           c0.104-0.026,0.183-0.026,0.288-0.052h0.026c0.55-0.026,1.099-0.079,1.649-0.157c1.152-0.105,2.303-0.236,3.455-0.367           c11.333-1.309,22.326-3.402,32.821-6.203c0.68-0.183,1.387-0.366,2.067-0.55c0.681-0.208,1.361-0.392,2.042-0.575h0.027           c12.563-3.638,24.419-8.297,35.36-13.846c-27.875-17.222-56.744-27.692-79.595-33.973           c-12.04-3.324-22.431-5.471-30.126-6.805C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
								</g>
								<path style="opacity:0.42;fill:#F2F8FC;" d="M2023.123,592.271c0.079,0.418,0.158,0.864,0.236,1.309          c1.256,6.674,3.952,15.259,8.846,26.252c5.078,4.607,10.287,9.03,15.574,13.296c1.1,0.89,2.199,1.78,3.323,2.644          c4.084,3.219,8.219,6.36,12.381,9.397c0.052,0.052,0.078,0.077,0.13,0.077c0.864,0.68,1.754,1.309,2.618,1.937          c0.34,0.236,0.68,0.498,1.021,0.733h0.026v0.026c0,0,0.026,0,0.026,0.026c0.418,0.262,0.838,0.576,1.257,0.864          c2.486,1.78,4.973,3.507,7.459,5.182c1.257,0.864,2.513,1.727,3.796,2.566c0.026,0,0.026,0.026,0.052,0.026          c0.969,0.681,1.963,1.309,2.932,1.937c4.92,3.246,9.815,6.36,14.683,9.318c1.571,0.968,3.141,1.911,4.71,2.852          c1.938,1.152,3.874,2.278,5.785,3.376c4.24,2.488,8.454,4.869,12.589,7.119c0.969,0.524,1.937,1.047,2.879,1.545          c1.544,0.838,3.062,1.648,4.58,2.486c0.785,0.393,1.571,0.812,2.329,1.204c6.256,3.271,12.25,6.256,17.982,9.03          c0.811,0.393,1.623,0.759,2.408,1.125c0.209,0.13,0.418,0.235,0.628,0.314c0.785,0.393,1.571,0.759,2.356,1.125          c2.931,1.388,5.81,2.697,8.559,3.926c1.544,0.706,3.063,1.388,4.528,2.042c6.439,2.826,12.224,5.261,17.17,7.25          c3.586,1.439,7.224,2.224,10.732,2.408c10.365,0.497,19.368-4.241,21.174-12.041v-0.026c0.209-0.811,0.315-1.648,0.34-2.512          c0.104-3.9-0.236-8.637-1.282-14.317c-1.204-6.701-3.324-14.736-6.727-24.368c-16.228-19.735-35.413-35.204-55.069-47.348          c-27.875-17.222-56.744-27.692-79.595-33.973c-12.04-3.324-22.431-5.471-30.126-6.805          C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
								<path style="opacity:0.42;fill:#F2F8FC;" d="M2023.123,592.271c0.079,0.418,0.158,0.864,0.236,1.309          c0.471,0.209,0.941,0.418,1.413,0.628c6.439,2.826,12.223,5.261,17.17,7.25c0.497,0.21,0.995,0.393,1.492,0.55          c2.434,0.864,4.895,1.414,7.303,1.676c0.655,0.104,1.282,0.157,1.937,0.183c0.287,0.027,0.575,0.027,0.863,0.027h0.994          c0.969-0.027,1.937-0.08,2.854-0.183c0.13,0,0.261-0.026,0.392-0.026c1.571-0.21,3.089-0.524,4.501-0.969          c5.916-1.806,10.366-5.654,11.569-10.914c0.209-0.812,0.314-1.648,0.34-2.512c0.079-2.906-0.104-6.308-0.628-10.208          c-12.04-3.324-22.431-5.471-30.126-6.805C2031.604,570.207,2021.134,580.44,2023.123,592.271z"/>
							</g>
						</g>
						<g>
							<path style="opacity:0.05;fill:#FFFFFF;" d="M1468.811,564.26c0,0-72.605,93.914-90.497,257.818         c-1.859,17.028,12.866,31.252,29.79,28.607l0.52-0.081c11.235-1.755,20.014-10.627,21.653-21.878L1468.811,564.26z"/>
							<path style="opacity:0.05;fill:#FFFFFF;" d="M1954.728,564.26c0,0,72.605,93.914,90.497,257.818         c1.859,17.028-12.866,31.252-29.79,28.607l-0.52-0.081c-11.234-1.755-20.013-10.627-21.653-21.878L1954.728,564.26z"/>
						</g>
						<g>
							<g>
								<path style="opacity:0.11;fill:#090C09;" d="M1118.682,996.788l0.643,30.943l0.814,45.326          c-15.53,3.901-22.975,13.069-26.607,21.536c-2.028,4.728-2.866,9.125-3.164,12.45c-0.35,3.399-2.398,6.426-5.405,8.042          l-0.233,0.17l-25.524,13.887l-1.02,0.563l-29.368,15.97l-51.823,28.233c-3.356,1.87-7.128,1.647-10.091-0.02l-0.032-2.943          l-0.461-22.785l-0.204-10.644c-0.011-1.242,0.127-2.379,0.329-3.526c2.41-11.866,7.273-23.158,13.932-33.899          c2.187-3.771,4.778-7.478,7.402-10.995c0.053-0.074,0.053-0.074,0.117-0.085c3.038-4.174,6.309-8.126,9.771-12.11          c5.236-5.918,10.833-11.633,16.685-16.999c5.746-5.216,11.588-10.252,17.493-14.906c3.59-2.826,7.254-5.6,10.823-8.16          c17.334-12.718,33.681-22.323,44.174-28.136c3.782-2.071,6.788-3.686,8.838-4.75c1.997-0.987,3.028-1.487,3.028-1.487          l3.324-1.669c0.053-0.074,0.245-0.106,0.297-0.18c3.952-1.839,7.563-2.965,10.75-3.497          C1115.144,996.79,1116.961,996.682,1118.682,996.788z"/>
								<path style="fill:#1E1F24;" d="M1118.489,986.994l0.193,9.794l0.643,30.943l0.814,45.326          c-15.53,3.901-22.975,13.069-26.607,21.536c-2.028,4.728-2.866,9.125-3.164,12.45c-0.35,3.399-2.398,6.426-5.405,8.042          l-0.233,0.17l-25.524,13.887l-1.02,0.563l-29.368,15.97l-51.823,28.233c-3.356,1.87-7.128,1.647-10.091-0.02          c-3.102-1.71-5.354-5.003-5.429-8.985l-0.257-13.715l-0.6-33.439c-0.064-1.168-0.001-2.358,0.265-3.516          c2.421-11.802,7.347-23.105,13.932-33.898c2.315-3.793,4.832-7.554,7.52-11.08c7.752-10.465,16.875-20.175,26.466-29.046          c11.418-10.487,23.483-19.835,34.795-27.815c21.008-14.642,39.533-24.544,46.596-28.146          c1.869-0.968,3.016-1.552,3.016-1.552l3.261-1.658c0.128-0.022,0.255-0.043,0.372-0.128L1118.489,986.994z"/>
								<g style="opacity:0.42;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000062878656323026565750000008528099331183717805_" filterUnits="userSpaceOnUse" x="960.039" y="981.138" width="159.545" height="194.316">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="960.039" y="981.138" width="159.545" height="194.316" id="SVGID_00000070814557999513253790000006106330045760742559_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000062878656323026565750000008528099331183717805_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000114764248891961083950000013608585888961312649_" filterUnits="userSpaceOnUse" x="960.039" y="981.138" width="159.545" height="194.316">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="960.039" y="981.138" width="159.545" height="194.316" id="SVGID_00000070814557999513253790000006106330045760742559_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000114764248891961083950000013608585888961312649_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000147922607974235854690000017878490299132104106_" gradientUnits="userSpaceOnUse" x1="143.5326" y1="1687.3138" x2="327.7964" y2="1687.3138" gradientTransform="matrix(0.9864 -0.1646 0.1646 0.9864 529.3822 -548.164)">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.028" style="stop-color:#EEEEEE"/>
												<stop offset="0.1037" style="stop-color:#C4C4C4"/>
												<stop offset="0.1811" style="stop-color:#A2A2A2"/>
												<stop offset="0.2597" style="stop-color:#888888"/>
												<stop offset="0.3397" style="stop-color:#757575"/>
												<stop offset="0.4222" style="stop-color:#6A6A6A"/>
												<stop offset="0.5108" style="stop-color:#666666"/>
												<stop offset="0.5714" style="stop-color:#6F6F6F"/>
												<stop offset="0.6716" style="stop-color:#888888"/>
												<stop offset="0.7989" style="stop-color:#B1B1B1"/>
												<stop offset="0.9468" style="stop-color:#E9E9E9"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000070814557999513253790000006106330045760742559_);fill:url(#SVGID_00000147922607974235854690000017878490299132104106_);" d="             M1117.933,987.222l0.193,9.793l0.643,30.944l0.814,45.325c-15.53,3.902-22.975,13.07-26.607,21.536             c-2.028,4.728-2.866,9.125-3.164,12.45c-0.349,3.399-2.399,6.426-5.405,8.042l-0.233,0.17l-25.524,13.887l-1.02,0.563             l-29.368,15.97l-51.823,28.232c-3.355,1.871-7.127,1.648-10.091-0.019c-3.102-1.71-5.355-5.003-5.43-8.985l-0.256-13.715             l-0.6-33.439c-0.064-1.169-0.001-2.358,0.264-3.516c2.421-11.802,7.347-23.105,13.932-33.899             c2.315-3.793,4.832-7.553,7.52-11.08c7.752-10.464,16.875-20.174,26.466-29.046             c11.418-10.486,23.483-19.835,34.794-27.815c21.009-14.641,39.534-24.544,46.597-28.146             c1.869-0.967,3.016-1.552,3.016-1.552l3.261-1.658c0.128-0.021,0.255-0.042,0.372-0.127L1117.933,987.222z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000070102486140475290860000015483710087962894244_" gradientUnits="userSpaceOnUse" x1="143.5326" y1="1687.3138" x2="327.7964" y2="1687.3138" gradientTransform="matrix(0.9864 -0.1646 0.1646 0.9864 529.3822 -548.164)">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0102" style="stop-color:#F2F2F2"/>
										<stop offset="0.0685" style="stop-color:#BABABA"/>
										<stop offset="0.1286" style="stop-color:#898989"/>
										<stop offset="0.1892" style="stop-color:#5F5F5F"/>
										<stop offset="0.2504" style="stop-color:#3C3C3C"/>
										<stop offset="0.3125" style="stop-color:#222222"/>
										<stop offset="0.3757" style="stop-color:#0F0F0F"/>
										<stop offset="0.4408" style="stop-color:#040404"/>
										<stop offset="0.5108" style="stop-color:#000000"/>
										<stop offset="0.5523" style="stop-color:#090909"/>
										<stop offset="0.6209" style="stop-color:#222222"/>
										<stop offset="0.7081" style="stop-color:#4B4B4B"/>
										<stop offset="0.8105" style="stop-color:#848484"/>
										<stop offset="0.9244" style="stop-color:#CBCBCB"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000070102486140475290860000015483710087962894244_);" d="M1117.933,987.222l0.193,9.793           l0.643,30.944l0.814,45.325c-15.53,3.902-22.975,13.07-26.607,21.536c-2.028,4.728-2.866,9.125-3.164,12.45           c-0.349,3.399-2.399,6.426-5.405,8.042l-0.233,0.17l-25.524,13.887l-1.02,0.563l-29.368,15.97l-51.823,28.232           c-3.355,1.871-7.127,1.648-10.091-0.019c-3.102-1.71-5.355-5.003-5.43-8.985l-0.256-13.715l-0.6-33.439           c-0.064-1.169-0.001-2.358,0.264-3.516c2.421-11.802,7.347-23.105,13.932-33.899c2.315-3.793,4.832-7.553,7.52-11.08           c7.752-10.464,16.875-20.174,26.466-29.046c11.418-10.486,23.483-19.835,34.794-27.815           c21.009-14.641,39.534-24.544,46.597-28.146c1.869-0.967,3.016-1.552,3.016-1.552l3.261-1.658           c0.128-0.021,0.255-0.042,0.372-0.127L1117.933,987.222z"/>
								</g>
								<path style="opacity:0.26;fill:#090C09;" d="M1118.519,987.006l0.79,40.68l0.852,45.336          c-15.521,3.952-23.022,13.107-26.618,21.576c-2.043,4.701-2.832,9.16-3.169,12.452c-0.319,3.391-2.408,6.397-5.401,8.055          l-0.282,0.149c-1.167-5.154-1.94-11.225-2.372-17.898c-1.097-16.372-0.288-36.437,1.257-55.567          c1.868-23.307,4.815-45.26,6.581-57.546c1.944-1.006,3.015-1.525,3.015-1.525l3.303-1.641          c0.089-0.083,0.222-0.105,0.315-0.155L1118.519,987.006z"/>
								<path style="fill:#898F9B;" d="M1093.532,1094.593c-2.028,4.728-2.866,9.125-3.164,12.45          c-0.35,3.399-2.398,6.426-5.405,8.042l-0.233,0.17l-25.524,13.887l-1.02,0.563l-29.368,15.97l-51.823,28.233          c-3.356,1.87-7.128,1.647-10.091-0.02c-3.102-1.71-5.354-5.003-5.429-8.985l-0.257-13.715          c1.678-0.934,3.409-1.944,5.194-3.028c13.319-7.724,31.163-17.383,51.111-26.607c8.146-3.782,16.633-7.491,25.365-10.913          c3.813-1.488,7.563-2.964,11.396-4.325c9.252-3.444,18.641-6.451,28.042-9.001          C1086.097,1096.358,1089.804,1095.412,1093.532,1094.593z"/>
								<g style="opacity:0.42;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000038388355603151039830000014506876032185945235_" filterUnits="userSpaceOnUse" x="961.217" y="1094.593" width="132.315" height="80.633">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="961.217" y="1094.593" width="132.315" height="80.633" id="SVGID_00000016785466822372977160000012257580558717747378_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000038388355603151039830000014506876032185945235_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000093899135225202282950000006633337115819475883_" filterUnits="userSpaceOnUse" x="961.217" y="1094.593" width="132.315" height="80.633">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="961.217" y="1094.593" width="132.315" height="80.633" id="SVGID_00000016785466822372977160000012257580558717747378_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000093899135225202282950000006633337115819475883_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000019655316559859376900000003328915372296196253_" gradientUnits="userSpaceOnUse" x1="144.1188" y1="1742.9723" x2="286.0574" y2="1742.9723" gradientTransform="matrix(0.9864 -0.1646 0.1646 0.9864 529.3822 -548.164)">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.028" style="stop-color:#EEEEEE"/>
												<stop offset="0.1037" style="stop-color:#C4C4C4"/>
												<stop offset="0.1811" style="stop-color:#A2A2A2"/>
												<stop offset="0.2597" style="stop-color:#888888"/>
												<stop offset="0.3397" style="stop-color:#757575"/>
												<stop offset="0.4222" style="stop-color:#6A6A6A"/>
												<stop offset="0.5108" style="stop-color:#666666"/>
												<stop offset="0.5714" style="stop-color:#6F6F6F"/>
												<stop offset="0.6716" style="stop-color:#888888"/>
												<stop offset="0.7989" style="stop-color:#B1B1B1"/>
												<stop offset="0.9468" style="stop-color:#E9E9E9"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000016785466822372977160000012257580558717747378_);fill:url(#SVGID_00000019655316559859376900000003328915372296196253_);" d="             M1093.532,1094.593c-2.028,4.728-2.866,9.125-3.164,12.45c-0.35,3.399-2.398,6.426-5.405,8.042l-0.233,0.17             l-25.524,13.887l-1.02,0.563l-29.368,15.97l-51.823,28.233c-3.356,1.87-7.128,1.647-10.091-0.02             c-3.102-1.71-5.354-5.003-5.429-8.985l-0.257-13.715c1.678-0.934,3.409-1.944,5.194-3.028             c13.319-7.724,31.163-17.383,51.111-26.607c8.146-3.782,16.633-7.491,25.365-10.913             c3.813-1.488,7.563-2.964,11.396-4.325c9.252-3.444,18.641-6.451,28.042-9.001             C1086.097,1096.358,1089.804,1095.412,1093.532,1094.593z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000145767062211392287540000012220070053637734831_" gradientUnits="userSpaceOnUse" x1="144.1188" y1="1742.9723" x2="286.0574" y2="1742.9723" gradientTransform="matrix(0.9864 -0.1646 0.1646 0.9864 529.3822 -548.164)">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0102" style="stop-color:#F2F2F2"/>
										<stop offset="0.0685" style="stop-color:#BABABA"/>
										<stop offset="0.1286" style="stop-color:#898989"/>
										<stop offset="0.1892" style="stop-color:#5F5F5F"/>
										<stop offset="0.2504" style="stop-color:#3C3C3C"/>
										<stop offset="0.3125" style="stop-color:#222222"/>
										<stop offset="0.3757" style="stop-color:#0F0F0F"/>
										<stop offset="0.4408" style="stop-color:#040404"/>
										<stop offset="0.5108" style="stop-color:#000000"/>
										<stop offset="0.5523" style="stop-color:#090909"/>
										<stop offset="0.6209" style="stop-color:#222222"/>
										<stop offset="0.7081" style="stop-color:#4B4B4B"/>
										<stop offset="0.8105" style="stop-color:#848484"/>
										<stop offset="0.9244" style="stop-color:#CBCBCB"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000145767062211392287540000012220070053637734831_);" d="M1093.532,1094.593           c-2.028,4.728-2.866,9.125-3.164,12.45c-0.35,3.399-2.398,6.426-5.405,8.042l-0.233,0.17l-25.524,13.887l-1.02,0.563           l-29.368,15.97l-51.823,28.233c-3.356,1.87-7.128,1.647-10.091-0.02c-3.102-1.71-5.354-5.003-5.429-8.985l-0.257-13.715           c1.678-0.934,3.409-1.944,5.194-3.028c13.319-7.724,31.163-17.383,51.111-26.607c8.146-3.782,16.633-7.491,25.365-10.913           c3.813-1.488,7.563-2.964,11.396-4.325c9.252-3.444,18.641-6.451,28.042-9.001           C1086.097,1096.358,1089.804,1095.412,1093.532,1094.593z"/>
								</g>
								<path style="opacity:0.05;fill:#FFFFFF;" d="M1028.793,1145.703l-51.781,28.226c-3.36,1.853-7.125,1.602-10.089-0.075          c-3.12-1.649-5.375-4.943-5.425-8.966l-0.27-13.703l-0.617-33.44c-0.043-1.181,0.026-2.329,0.293-3.511          c2.369-11.817,7.305-23.133,13.883-33.895c4.576,2.648,8.991,5.581,13.092,8.773c11.884,8.974,21.925,19.91,29.648,32.42          C1022.185,1129.026,1026.016,1137.122,1028.793,1145.703z"/>
								<path style="opacity:0.05;fill:#FFFFFF;" d="M1059.206,1129.142l-1.02,0.563l-29.368,15.97l-51.823,28.233          c-3.356,1.87-7.128,1.647-10.091-0.02c-3.102-1.71-5.354-5.003-5.429-8.985l-0.257-13.715l-0.6-33.439          c-0.064-1.168-0.001-2.358,0.265-3.516c2.421-11.802,7.347-23.105,13.932-33.898c2.315-3.793,4.832-7.554,7.52-11.08          c5.237,2.336,10.379,4.885,15.424,7.646c13.343,7.338,25.805,16.067,37.194,26.221c2.698,2.431,5.344,4.938,7.937,7.519          c5.269,5.279,10.263,10.866,14.916,16.771C1058.292,1127.986,1058.781,1128.559,1059.206,1129.142z"/>
								<path style="fill:#090C09;" d="M1137.222,1015.602c1.256,7.319,2.25,15.124,3.296,22.819          c5.077,37.171-20.357,34.601-20.357,34.601s-3.213-21.096-5.012-43.109c-0.69-8.436-1.161-16.976-1.162-24.539          c0.017-27.256-20.814-22.656-20.814-22.656l3.303-1.641c0.089-0.083,0.222-0.105,0.315-0.155          C1124.46,968.096,1132.6,988.71,1137.222,1015.602z"/>
								<path style="opacity:0.11;fill:#090C09;" d="M1137.222,1015.602c-5.595,4.442-11.567,8.47-17.913,12.084          c-1.375,0.74-2.745,1.513-4.16,2.227c-9.775,5.105-20.349,9.117-31.572,11.876c-3.563,0.901-7.187,1.642-10.899,2.261          c-22.24,3.71-44.038,2.1-63.872-3.857c32.33-29.819,70.493-50.391,81.352-55.95c1.944-1.006,3.015-1.525,3.015-1.525          l3.303-1.641c0.089-0.083,0.222-0.105,0.315-0.155C1124.46,968.096,1132.6,988.71,1137.222,1015.602z"/>
							</g>
							<g>
								<path style="opacity:0.11;fill:#090C09;" d="M2304.857,996.788l-0.643,30.943l-0.813,45.326          c15.529,3.901,22.975,13.069,26.607,21.536c2.027,4.728,2.865,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042          l0.233,0.17l25.524,13.887l1.02,0.563l29.368,15.97l51.823,28.233c3.355,1.87,7.127,1.647,10.091-0.02l0.033-2.943          l0.46-22.785l0.204-10.644c0.012-1.242-0.126-2.379-0.329-3.526c-2.409-11.866-7.273-23.158-13.931-33.899          c-2.187-3.771-4.779-7.478-7.402-10.995c-0.053-0.074-0.053-0.074-0.116-0.085c-3.039-4.174-6.309-8.126-9.771-12.11          c-5.236-5.918-10.833-11.633-16.686-16.999c-5.746-5.216-11.588-10.252-17.493-14.906c-3.59-2.826-7.254-5.6-10.823-8.16          c-17.334-12.718-33.68-22.323-44.174-28.136c-3.781-2.071-6.788-3.686-8.838-4.75c-1.997-0.987-3.027-1.487-3.027-1.487          l-3.324-1.669c-0.053-0.074-0.244-0.106-0.297-0.18c-3.952-1.839-7.563-2.965-10.75-3.497          C2308.395,996.79,2306.578,996.682,2304.857,996.788z"/>
								<path style="fill:#1E1F24;" d="M2305.05,986.994l-0.193,9.794l-0.643,30.943l-0.813,45.326          c15.529,3.901,22.975,13.069,26.607,21.536c2.027,4.728,2.865,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042          l0.233,0.17l25.524,13.887l1.02,0.563l29.368,15.97l51.823,28.233c3.355,1.87,7.127,1.647,10.091-0.02          c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715l0.599-33.439c0.064-1.168,0.001-2.358-0.264-3.516          c-2.421-11.802-7.348-23.105-13.932-33.898c-2.315-3.793-4.832-7.554-7.52-11.08c-7.752-10.465-16.875-20.175-26.466-29.046          c-11.417-10.487-23.483-19.835-34.794-27.815c-21.009-14.642-39.534-24.544-46.596-28.146          c-1.87-0.968-3.017-1.552-3.017-1.552l-3.261-1.658c-0.128-0.022-0.255-0.043-0.372-0.128L2305.05,986.994z"/>
								<g style="opacity:0.42;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000062891680999261870950000008362549410753248389_" filterUnits="userSpaceOnUse" x="2303.957" y="981.138" width="159.544" height="194.316">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2303.957" y="981.138" width="159.544" height="194.316" id="SVGID_00000142166393123937708990000001258638557663769995_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000062891680999261870950000008362549410753248389_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000090994083102872510740000015191681226029039020_" filterUnits="userSpaceOnUse" x="2303.957" y="981.138" width="159.544" height="194.316">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2303.957" y="981.138" width="159.544" height="194.316" id="SVGID_00000142166393123937708990000001258638557663769995_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000090994083102872510740000015191681226029039020_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000021081101828422433660000008641188066950945941_" gradientUnits="userSpaceOnUse" x1="-6179.9331" y1="632.1208" x2="-5995.6689" y2="632.1208" gradientTransform="matrix(-0.9864 -0.1646 -0.1646 0.9864 -3516.9944 -548.164)">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.028" style="stop-color:#EEEEEE"/>
												<stop offset="0.1037" style="stop-color:#C4C4C4"/>
												<stop offset="0.1811" style="stop-color:#A2A2A2"/>
												<stop offset="0.2597" style="stop-color:#888888"/>
												<stop offset="0.3397" style="stop-color:#757575"/>
												<stop offset="0.4222" style="stop-color:#6A6A6A"/>
												<stop offset="0.5108" style="stop-color:#666666"/>
												<stop offset="0.5714" style="stop-color:#6F6F6F"/>
												<stop offset="0.6716" style="stop-color:#888888"/>
												<stop offset="0.7989" style="stop-color:#B1B1B1"/>
												<stop offset="0.9468" style="stop-color:#E9E9E9"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000142166393123937708990000001258638557663769995_);fill:url(#SVGID_00000021081101828422433660000008641188066950945941_);" d="             M2305.606,987.222l-0.193,9.793l-0.642,30.944l-0.814,45.325c15.529,3.902,22.975,13.07,26.607,21.536             c2.027,4.728,2.866,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042l0.234,0.17l25.523,13.887l1.02,0.563             l29.368,15.97l51.823,28.232c3.355,1.871,7.127,1.648,10.091-0.019c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715             l0.6-33.439c0.064-1.169,0.001-2.358-0.264-3.516c-2.421-11.802-7.348-23.105-13.932-33.899             c-2.315-3.793-4.832-7.553-7.52-11.08c-7.752-10.464-16.874-20.174-26.465-29.046             c-11.418-10.486-23.484-19.835-34.795-27.815c-21.009-14.641-39.534-24.544-46.596-28.146             c-1.87-0.967-3.017-1.552-3.017-1.552l-3.261-1.658c-0.128-0.021-0.255-0.042-0.372-0.127L2305.606,987.222z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000143591418984932287560000004234148113015444360_" gradientUnits="userSpaceOnUse" x1="-6179.9331" y1="632.1208" x2="-5995.6689" y2="632.1208" gradientTransform="matrix(-0.9864 -0.1646 -0.1646 0.9864 -3516.9944 -548.164)">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0102" style="stop-color:#F2F2F2"/>
										<stop offset="0.0685" style="stop-color:#BABABA"/>
										<stop offset="0.1286" style="stop-color:#898989"/>
										<stop offset="0.1892" style="stop-color:#5F5F5F"/>
										<stop offset="0.2504" style="stop-color:#3C3C3C"/>
										<stop offset="0.3125" style="stop-color:#222222"/>
										<stop offset="0.3757" style="stop-color:#0F0F0F"/>
										<stop offset="0.4408" style="stop-color:#040404"/>
										<stop offset="0.5108" style="stop-color:#000000"/>
										<stop offset="0.5523" style="stop-color:#090909"/>
										<stop offset="0.6209" style="stop-color:#222222"/>
										<stop offset="0.7081" style="stop-color:#4B4B4B"/>
										<stop offset="0.8105" style="stop-color:#848484"/>
										<stop offset="0.9244" style="stop-color:#CBCBCB"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000143591418984932287560000004234148113015444360_);" d="M2305.606,987.222l-0.193,9.793           l-0.642,30.944l-0.814,45.325c15.529,3.902,22.975,13.07,26.607,21.536c2.027,4.728,2.866,9.125,3.163,12.45           c0.35,3.399,2.399,6.426,5.405,8.042l0.234,0.17l25.523,13.887l1.02,0.563l29.368,15.97l51.823,28.232           c3.355,1.871,7.127,1.648,10.091-0.019c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715l0.6-33.439           c0.064-1.169,0.001-2.358-0.264-3.516c-2.421-11.802-7.348-23.105-13.932-33.899c-2.315-3.793-4.832-7.553-7.52-11.08           c-7.752-10.464-16.874-20.174-26.465-29.046c-11.418-10.486-23.484-19.835-34.795-27.815           c-21.009-14.641-39.534-24.544-46.596-28.146c-1.87-0.967-3.017-1.552-3.017-1.552l-3.261-1.658           c-0.128-0.021-0.255-0.042-0.372-0.127L2305.606,987.222z"/>
								</g>
								<path style="opacity:0.26;fill:#090C09;" d="M2305.021,987.006l-0.79,40.68l-0.853,45.336          c15.522,3.952,23.022,13.107,26.618,21.576c2.043,4.701,2.832,9.16,3.169,12.452c0.319,3.391,2.408,6.397,5.402,8.055          l0.281,0.149c1.168-5.154,1.94-11.225,2.372-17.898c1.098-16.372,0.288-36.437-1.256-55.567          c-1.869-23.307-4.816-45.26-6.581-57.546c-1.945-1.006-3.016-1.525-3.016-1.525l-3.302-1.641          c-0.09-0.083-0.222-0.105-0.315-0.155L2305.021,987.006z"/>
								<path style="fill:#898F9B;" d="M2330.008,1094.593c2.027,4.728,2.865,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042          l0.233,0.17l25.524,13.887l1.02,0.563l29.368,15.97l51.823,28.233c3.355,1.87,7.127,1.647,10.091-0.02          c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715c-1.679-0.934-3.409-1.944-5.194-3.028          c-13.319-7.724-31.163-17.383-51.111-26.607c-8.147-3.782-16.634-7.491-25.365-10.913          c-3.813-1.488-7.563-2.964-11.397-4.325c-9.251-3.444-18.641-6.451-28.042-9.001          C2337.442,1096.358,2333.735,1095.412,2330.008,1094.593z"/>
								<g style="opacity:0.42;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000050650518088716554510000008593013839463744684_" filterUnits="userSpaceOnUse" x="2330.008" y="1094.593" width="132.314" height="80.633">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2330.008" y="1094.593" width="132.314" height="80.633" id="SVGID_00000124161802543347991230000010099718119859428512_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000050650518088716554510000008593013839463744684_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000025415707303069648830000001705613350115579036_" filterUnits="userSpaceOnUse" x="2330.008" y="1094.593" width="132.314" height="80.633">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2330.008" y="1094.593" width="132.314" height="80.633" id="SVGID_00000124161802543347991230000010099718119859428512_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000025415707303069648830000001705613350115579036_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000130635810209630549900000014086937422782809769_" gradientUnits="userSpaceOnUse" x1="-6179.3472" y1="687.7792" x2="-6037.4087" y2="687.7792" gradientTransform="matrix(-0.9864 -0.1646 -0.1646 0.9864 -3516.9944 -548.164)">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.028" style="stop-color:#EEEEEE"/>
												<stop offset="0.1037" style="stop-color:#C4C4C4"/>
												<stop offset="0.1811" style="stop-color:#A2A2A2"/>
												<stop offset="0.2597" style="stop-color:#888888"/>
												<stop offset="0.3397" style="stop-color:#757575"/>
												<stop offset="0.4222" style="stop-color:#6A6A6A"/>
												<stop offset="0.5108" style="stop-color:#666666"/>
												<stop offset="0.5714" style="stop-color:#6F6F6F"/>
												<stop offset="0.6716" style="stop-color:#888888"/>
												<stop offset="0.7989" style="stop-color:#B1B1B1"/>
												<stop offset="0.9468" style="stop-color:#E9E9E9"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000124161802543347991230000010099718119859428512_);fill:url(#SVGID_00000130635810209630549900000014086937422782809769_);" d="             M2330.008,1094.593c2.027,4.728,2.865,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042l0.233,0.17l25.524,13.887             l1.02,0.563l29.368,15.97l51.823,28.233c3.355,1.87,7.127,1.647,10.091-0.02c3.103-1.71,5.355-5.003,5.43-8.985             l0.257-13.715c-1.679-0.934-3.409-1.944-5.194-3.028c-13.319-7.724-31.163-17.383-51.111-26.607             c-8.147-3.782-16.634-7.491-25.365-10.913c-3.813-1.488-7.563-2.964-11.397-4.325             c-9.251-3.444-18.641-6.451-28.042-9.001C2337.442,1096.358,2333.735,1095.412,2330.008,1094.593z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000088849567727840417210000008774135349389193407_" gradientUnits="userSpaceOnUse" x1="-6179.3472" y1="687.7792" x2="-6037.4087" y2="687.7792" gradientTransform="matrix(-0.9864 -0.1646 -0.1646 0.9864 -3516.9944 -548.164)">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0102" style="stop-color:#F2F2F2"/>
										<stop offset="0.0685" style="stop-color:#BABABA"/>
										<stop offset="0.1286" style="stop-color:#898989"/>
										<stop offset="0.1892" style="stop-color:#5F5F5F"/>
										<stop offset="0.2504" style="stop-color:#3C3C3C"/>
										<stop offset="0.3125" style="stop-color:#222222"/>
										<stop offset="0.3757" style="stop-color:#0F0F0F"/>
										<stop offset="0.4408" style="stop-color:#040404"/>
										<stop offset="0.5108" style="stop-color:#000000"/>
										<stop offset="0.5523" style="stop-color:#090909"/>
										<stop offset="0.6209" style="stop-color:#222222"/>
										<stop offset="0.7081" style="stop-color:#4B4B4B"/>
										<stop offset="0.8105" style="stop-color:#848484"/>
										<stop offset="0.9244" style="stop-color:#CBCBCB"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000088849567727840417210000008774135349389193407_);" d="M2330.008,1094.593           c2.027,4.728,2.865,9.125,3.163,12.45c0.35,3.399,2.399,6.426,5.405,8.042l0.233,0.17l25.524,13.887l1.02,0.563           l29.368,15.97l51.823,28.233c3.355,1.87,7.127,1.647,10.091-0.02c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715           c-1.679-0.934-3.409-1.944-5.194-3.028c-13.319-7.724-31.163-17.383-51.111-26.607           c-8.147-3.782-16.634-7.491-25.365-10.913c-3.813-1.488-7.563-2.964-11.397-4.325c-9.251-3.444-18.641-6.451-28.042-9.001           C2337.442,1096.358,2333.735,1095.412,2330.008,1094.593z"/>
								</g>
								<path style="opacity:0.05;fill:#FFFFFF;" d="M2394.746,1145.703l51.781,28.226c3.36,1.853,7.125,1.602,10.089-0.075          c3.12-1.649,5.375-4.943,5.426-8.966l0.269-13.703l0.618-33.44c0.043-1.181-0.025-2.329-0.293-3.511          c-2.369-11.817-7.305-23.133-13.883-33.895c-4.575,2.648-8.991,5.581-13.092,8.773c-11.884,8.974-21.925,19.91-29.648,32.42          C2401.354,1129.026,2397.523,1137.122,2394.746,1145.703z"/>
								<path style="opacity:0.05;fill:#FFFFFF;" d="M2364.333,1129.142l1.02,0.563l29.368,15.97l51.823,28.233          c3.355,1.87,7.127,1.647,10.091-0.02c3.103-1.71,5.355-5.003,5.43-8.985l0.257-13.715l0.599-33.439          c0.064-1.168,0.001-2.358-0.264-3.516c-2.421-11.802-7.348-23.105-13.932-33.898c-2.315-3.793-4.832-7.554-7.52-11.08          c-5.236,2.336-10.379,4.885-15.424,7.646c-13.343,7.338-25.804,16.067-37.194,26.221c-2.698,2.431-5.344,4.938-7.936,7.519          c-5.269,5.279-10.263,10.866-14.917,16.771C2365.247,1127.986,2364.758,1128.559,2364.333,1129.142z"/>
								<path style="fill:#090C09;" d="M2286.317,1015.602c-1.256,7.319-2.25,15.124-3.296,22.819          c-5.076,37.171,20.357,34.601,20.357,34.601s3.213-21.096,5.013-43.109c0.69-8.436,1.162-16.976,1.163-24.539          c-0.018-27.256,20.813-22.656,20.813-22.656l-3.302-1.641c-0.09-0.083-0.222-0.105-0.315-0.155          C2299.079,968.096,2290.94,988.71,2286.317,1015.602z"/>
								<path style="opacity:0.11;fill:#090C09;" d="M2286.317,1015.602c5.595,4.442,11.567,8.47,17.914,12.084          c1.375,0.74,2.745,1.513,4.16,2.227c9.775,5.105,20.349,9.117,31.573,11.876c3.562,0.901,7.187,1.642,10.899,2.261          c22.24,3.71,44.038,2.1,63.871-3.857c-32.329-29.819-70.492-50.391-81.351-55.95c-1.945-1.006-3.016-1.525-3.016-1.525          l-3.302-1.641c-0.09-0.083-0.222-0.105-0.315-0.155C2299.079,968.096,2290.94,988.71,2286.317,1015.602z"/>
							</g>
						</g>
					</g>
					<g>
						<path style="fill:#FFFFFF;" d="M1804.456,712.909l-89.35-45.295c-0.398-0.201-0.822-0.221-1.236-0.34        c-0.11-0.039-0.219-0.059-0.332-0.092c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.08-1.768,0.221        c-0.114,0.033-0.223,0.053-0.333,0.092c-0.414,0.12-0.838,0.14-1.235,0.34l-89.35,45.295        c-3.433,1.743-4.804,5.935-3.066,9.368c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.241,3.144-0.752l65.96-33.439        c12.839-6.509,28.01-6.509,40.849,0l65.96,33.439c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818        C1809.26,718.844,1807.89,714.653,1804.456,712.909z"/>
						<path style="fill:#FFFFFF;" d="M1804.456,668.756l-89.35-45.297c-0.398-0.201-0.822-0.221-1.236-0.34        c-0.11-0.039-0.219-0.059-0.332-0.091c-0.597-0.141-1.172-0.24-1.769-0.222c-0.598-0.018-1.172,0.08-1.768,0.222        c-0.114,0.032-0.223,0.052-0.333,0.091c-0.414,0.119-0.838,0.14-1.235,0.34l-89.35,45.297        c-3.433,1.742-4.804,5.934-3.066,9.367c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.439        c12.839-6.509,28.01-6.509,40.849,0l65.96,33.439c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.396,6.224-3.818        C1809.26,674.69,1807.89,670.498,1804.456,668.756z"/>
						<path style="fill:#FFFFFF;" d="M1804.456,624.601l-89.35-45.295c-0.398-0.202-0.822-0.222-1.236-0.34        c-0.11-0.039-0.219-0.06-0.332-0.092c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.079-1.768,0.221        c-0.114,0.032-0.223,0.053-0.333,0.092c-0.414,0.119-0.838,0.139-1.235,0.34l-89.35,45.295        c-3.433,1.742-4.804,5.935-3.066,9.368c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.44        c12.839-6.508,28.01-6.508,40.849,0l65.96,33.44c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818        C1809.26,630.536,1807.89,626.343,1804.456,624.601z"/>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000163753057321198345590000009511983045831965586_" filterUnits="userSpaceOnUse" x="1615.263" y="666.959" width="193.012" height="59.137">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1615.263" y="666.959" width="193.012" height="59.137" id="SVGID_00000004506505708510578410000003353002160000543159_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000163753057321198345590000009511983045831965586_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000043445056053289300700000016131455746243457718_" filterUnits="userSpaceOnUse" x="1615.263" y="666.959" width="193.012" height="59.137">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1615.263" y="666.959" width="193.012" height="59.137" id="SVGID_00000004506505708510578410000003353002160000543159_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000043445056053289300700000016131455746243457718_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000163765537125794258380000013419315869174078876_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="696.5269" x2="1808.2756" y2="696.5269">
									<stop offset="0" style="stop-color:#FFFFFF"/>
									<stop offset="0.0074" style="stop-color:#EEEEEE"/>
									<stop offset="0.0273" style="stop-color:#C4C4C4"/>
									<stop offset="0.0477" style="stop-color:#A2A2A2"/>
									<stop offset="0.0683" style="stop-color:#888888"/>
									<stop offset="0.0894" style="stop-color:#757575"/>
									<stop offset="0.1111" style="stop-color:#6A6A6A"/>
									<stop offset="0.1344" style="stop-color:#666666"/>
									<stop offset="0.3495" style="stop-color:#666666"/>
									<stop offset="0.4821" style="stop-color:#5D5D5D"/>
									<stop offset="0.7004" style="stop-color:#444444"/>
									<stop offset="0.8226" style="stop-color:#333333"/>
									<stop offset="1" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<path style="mask:url(#SVGID_00000004506505708510578410000003353002160000543159_);fill:url(#SVGID_00000163765537125794258380000013419315869174078876_);" d="          M1804.456,712.909l-89.35-45.295c-0.398-0.201-0.822-0.221-1.236-0.34c-0.11-0.039-0.219-0.059-0.332-0.092          c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.08-1.768,0.221c-0.114,0.033-0.223,0.053-0.333,0.092          c-0.414,0.12-0.838,0.14-1.235,0.34l-89.35,45.295c-3.433,1.743-4.804,5.935-3.066,9.368          c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.241,3.144-0.752l65.96-33.439c12.839-6.509,28.01-6.509,40.849,0          l65.96,33.439c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818          C1809.26,718.844,1807.89,714.653,1804.456,712.909z"/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000108307479679984262330000007407254469933966782_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="696.5269" x2="1808.2756" y2="696.5269">
							<stop offset="0" style="stop-color:#FDFDFD"/>
							<stop offset="0.0027" style="stop-color:#F2F2F2"/>
							<stop offset="0.018" style="stop-color:#BABABA"/>
							<stop offset="0.0338" style="stop-color:#898989"/>
							<stop offset="0.0498" style="stop-color:#5F5F5F"/>
							<stop offset="0.0659" style="stop-color:#3C3C3C"/>
							<stop offset="0.0822" style="stop-color:#222222"/>
							<stop offset="0.0989" style="stop-color:#0F0F0F"/>
							<stop offset="0.116" style="stop-color:#040404"/>
							<stop offset="0.1344" style="stop-color:#000000"/>
							<stop offset="0.1553" style="stop-color:#090909"/>
							<stop offset="0.1899" style="stop-color:#222222"/>
							<stop offset="0.2339" style="stop-color:#4A4B4B"/>
							<stop offset="0.2855" style="stop-color:#828384"/>
							<stop offset="0.3429" style="stop-color:#C9CACB"/>
							<stop offset="0.3495" style="stop-color:#D1D3D4"/>
							<stop offset="0.4084" style="stop-color:#C8CACB"/>
							<stop offset="0.5058" style="stop-color:#B0B1B2"/>
							<stop offset="0.6294" style="stop-color:#888889"/>
							<stop offset="0.7733" style="stop-color:#525051"/>
							<stop offset="0.8226" style="stop-color:#3E3B3C"/>
							<stop offset="1" style="stop-color:#FFFFFF"/>
						</linearGradient>
						<path style="opacity:0.4;fill:url(#SVGID_00000108307479679984262330000007407254469933966782_);" d="M1804.456,712.909        l-89.35-45.295c-0.398-0.201-0.822-0.221-1.236-0.34c-0.11-0.039-0.219-0.059-0.332-0.092        c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.08-1.768,0.221c-0.114,0.033-0.223,0.053-0.333,0.092        c-0.414,0.12-0.838,0.14-1.235,0.34l-89.35,45.295c-3.433,1.743-4.804,5.935-3.066,9.368c1.229,2.423,3.679,3.818,6.224,3.818        c1.057,0,2.133-0.241,3.144-0.752l65.96-33.439c12.839-6.509,28.01-6.509,40.849,0l65.96,33.439        c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818C1809.26,718.844,1807.89,714.653,1804.456,712.909z"/>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000142169154996767360700000011286207978192791710_" filterUnits="userSpaceOnUse" x="1615.263" y="622.804" width="193.012" height="59.137">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1615.263" y="622.804" width="193.012" height="59.137" id="SVGID_00000181803527490680572340000000675792595396608647_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000142169154996767360700000011286207978192791710_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000138552671140069436380000013573457463364679591_" filterUnits="userSpaceOnUse" x="1615.263" y="622.804" width="193.012" height="59.137">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1615.263" y="622.804" width="193.012" height="59.137" id="SVGID_00000181803527490680572340000000675792595396608647_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000138552671140069436380000013573457463364679591_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000145756739261227309860000007014745607047297727_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="652.3726" x2="1808.2756" y2="652.3726">
									<stop offset="0" style="stop-color:#FFFFFF"/>
									<stop offset="0.0074" style="stop-color:#EEEEEE"/>
									<stop offset="0.0273" style="stop-color:#C4C4C4"/>
									<stop offset="0.0477" style="stop-color:#A2A2A2"/>
									<stop offset="0.0683" style="stop-color:#888888"/>
									<stop offset="0.0894" style="stop-color:#757575"/>
									<stop offset="0.1111" style="stop-color:#6A6A6A"/>
									<stop offset="0.1344" style="stop-color:#666666"/>
									<stop offset="0.3495" style="stop-color:#666666"/>
									<stop offset="0.4821" style="stop-color:#5D5D5D"/>
									<stop offset="0.7004" style="stop-color:#444444"/>
									<stop offset="0.8226" style="stop-color:#333333"/>
									<stop offset="1" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<path style="mask:url(#SVGID_00000181803527490680572340000000675792595396608647_);fill:url(#SVGID_00000145756739261227309860000007014745607047297727_);" d="          M1804.456,668.756l-89.35-45.297c-0.398-0.201-0.822-0.221-1.236-0.34c-0.11-0.039-0.219-0.059-0.332-0.091          c-0.597-0.141-1.172-0.24-1.769-0.222c-0.598-0.018-1.172,0.08-1.768,0.222c-0.114,0.032-0.223,0.052-0.333,0.091          c-0.414,0.119-0.838,0.14-1.235,0.34l-89.35,45.297c-3.433,1.742-4.804,5.934-3.066,9.367          c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.439c12.839-6.509,28.01-6.509,40.849,0          l65.96,33.439c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.396,6.224-3.818          C1809.26,674.69,1807.89,670.498,1804.456,668.756z"/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000041256861624589935450000004908280420460976020_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="652.3726" x2="1808.2756" y2="652.3726">
							<stop offset="0" style="stop-color:#FDFDFD"/>
							<stop offset="0.0027" style="stop-color:#F2F2F2"/>
							<stop offset="0.018" style="stop-color:#BABABA"/>
							<stop offset="0.0338" style="stop-color:#898989"/>
							<stop offset="0.0498" style="stop-color:#5F5F5F"/>
							<stop offset="0.0659" style="stop-color:#3C3C3C"/>
							<stop offset="0.0822" style="stop-color:#222222"/>
							<stop offset="0.0989" style="stop-color:#0F0F0F"/>
							<stop offset="0.116" style="stop-color:#040404"/>
							<stop offset="0.1344" style="stop-color:#000000"/>
							<stop offset="0.1553" style="stop-color:#090909"/>
							<stop offset="0.1899" style="stop-color:#222222"/>
							<stop offset="0.2339" style="stop-color:#4A4B4B"/>
							<stop offset="0.2855" style="stop-color:#828384"/>
							<stop offset="0.3429" style="stop-color:#C9CACB"/>
							<stop offset="0.3495" style="stop-color:#D1D3D4"/>
							<stop offset="0.4084" style="stop-color:#C8CACB"/>
							<stop offset="0.5058" style="stop-color:#B0B1B2"/>
							<stop offset="0.6294" style="stop-color:#888889"/>
							<stop offset="0.7733" style="stop-color:#525051"/>
							<stop offset="0.8226" style="stop-color:#3E3B3C"/>
							<stop offset="1" style="stop-color:#FFFFFF"/>
						</linearGradient>
						<path style="opacity:0.4;fill:url(#SVGID_00000041256861624589935450000004908280420460976020_);" d="M1804.456,668.756        l-89.35-45.297c-0.398-0.201-0.822-0.221-1.236-0.34c-0.11-0.039-0.219-0.059-0.332-0.091        c-0.597-0.141-1.172-0.24-1.769-0.222c-0.598-0.018-1.172,0.08-1.768,0.222c-0.114,0.032-0.223,0.052-0.333,0.091        c-0.414,0.119-0.838,0.14-1.235,0.34l-89.35,45.297c-3.433,1.742-4.804,5.934-3.066,9.367        c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.439c12.839-6.509,28.01-6.509,40.849,0        l65.96,33.439c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.396,6.224-3.818        C1809.26,674.69,1807.89,670.498,1804.456,668.756z"/>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000023960104635021369450000006300501513624131978_" filterUnits="userSpaceOnUse" x="1615.263" y="578.65" width="193.012" height="59.137">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1615.263" y="578.65" width="193.012" height="59.137" id="SVGID_00000075841727315428094570000006386922854802675354_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000023960104635021369450000006300501513624131978_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000060751293121271654990000004052916732182829467_" filterUnits="userSpaceOnUse" x="1615.263" y="578.65" width="193.012" height="59.137">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1615.263" y="578.65" width="193.012" height="59.137" id="SVGID_00000075841727315428094570000006386922854802675354_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000060751293121271654990000004052916732182829467_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000132768996600011020010000001498310057775794334_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="608.2185" x2="1808.2756" y2="608.2185">
									<stop offset="0" style="stop-color:#FFFFFF"/>
									<stop offset="0.0074" style="stop-color:#EEEEEE"/>
									<stop offset="0.0273" style="stop-color:#C4C4C4"/>
									<stop offset="0.0477" style="stop-color:#A2A2A2"/>
									<stop offset="0.0683" style="stop-color:#888888"/>
									<stop offset="0.0894" style="stop-color:#757575"/>
									<stop offset="0.1111" style="stop-color:#6A6A6A"/>
									<stop offset="0.1344" style="stop-color:#666666"/>
									<stop offset="0.3495" style="stop-color:#666666"/>
									<stop offset="0.4821" style="stop-color:#5D5D5D"/>
									<stop offset="0.7004" style="stop-color:#444444"/>
									<stop offset="0.8226" style="stop-color:#333333"/>
									<stop offset="1" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<path style="mask:url(#SVGID_00000075841727315428094570000006386922854802675354_);fill:url(#SVGID_00000132768996600011020010000001498310057775794334_);" d="          M1804.456,624.601l-89.35-45.295c-0.398-0.202-0.822-0.222-1.236-0.34c-0.11-0.039-0.219-0.06-0.332-0.092          c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.079-1.768,0.221c-0.114,0.032-0.223,0.053-0.333,0.092          c-0.414,0.119-0.838,0.139-1.235,0.34l-89.35,45.295c-3.433,1.742-4.804,5.935-3.066,9.368          c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.44c12.839-6.508,28.01-6.508,40.849,0          l65.96,33.44c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818          C1809.26,630.536,1807.89,626.343,1804.456,624.601z"/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000054944279017606256280000003566548104771006132_" gradientUnits="userSpaceOnUse" x1="1615.2633" y1="608.2185" x2="1808.2756" y2="608.2185">
							<stop offset="0" style="stop-color:#FDFDFD"/>
							<stop offset="0.0027" style="stop-color:#F2F2F2"/>
							<stop offset="0.018" style="stop-color:#BABABA"/>
							<stop offset="0.0338" style="stop-color:#898989"/>
							<stop offset="0.0498" style="stop-color:#5F5F5F"/>
							<stop offset="0.0659" style="stop-color:#3C3C3C"/>
							<stop offset="0.0822" style="stop-color:#222222"/>
							<stop offset="0.0989" style="stop-color:#0F0F0F"/>
							<stop offset="0.116" style="stop-color:#040404"/>
							<stop offset="0.1344" style="stop-color:#000000"/>
							<stop offset="0.1553" style="stop-color:#090909"/>
							<stop offset="0.1899" style="stop-color:#222222"/>
							<stop offset="0.2339" style="stop-color:#4A4B4B"/>
							<stop offset="0.2855" style="stop-color:#828384"/>
							<stop offset="0.3429" style="stop-color:#C9CACB"/>
							<stop offset="0.3495" style="stop-color:#D1D3D4"/>
							<stop offset="0.4084" style="stop-color:#C8CACB"/>
							<stop offset="0.5058" style="stop-color:#B0B1B2"/>
							<stop offset="0.6294" style="stop-color:#888889"/>
							<stop offset="0.7733" style="stop-color:#525051"/>
							<stop offset="0.8226" style="stop-color:#3E3B3C"/>
							<stop offset="1" style="stop-color:#FFFFFF"/>
						</linearGradient>
						<path style="opacity:0.4;fill:url(#SVGID_00000054944279017606256280000003566548104771006132_);" d="M1804.456,624.601        l-89.35-45.295c-0.398-0.202-0.822-0.222-1.236-0.34c-0.11-0.039-0.219-0.06-0.332-0.092        c-0.597-0.141-1.172-0.239-1.769-0.221c-0.598-0.018-1.172,0.079-1.768,0.221c-0.114,0.032-0.223,0.053-0.333,0.092        c-0.414,0.119-0.838,0.139-1.235,0.34l-89.35,45.295c-3.433,1.742-4.804,5.935-3.066,9.368        c1.229,2.423,3.679,3.818,6.224,3.818c1.057,0,2.133-0.242,3.144-0.752l65.96-33.44c12.839-6.508,28.01-6.508,40.849,0        l65.96,33.44c1.011,0.51,2.087,0.752,3.144,0.752c2.545,0,4.995-1.395,6.224-3.818        C1809.26,630.536,1807.89,626.343,1804.456,624.601z"/>
					</g>
				</g>
			</g>
			<g>
				<g>
					<g>
						<path style="fill:#FA2E0B;" d="M2413.045,2056.648l0.04,71.53l46.762-10.192c6.595-1.463,11.295-7.25,11.291-14.008        l-0.013-23.249c0-0.973-0.109-1.947-0.325-2.865c-1.084-5.461-5.358-9.892-10.981-11.078L2413.045,2056.648z"/>
						<path style="fill:#F2C52C;" d="M2413.045,2056.648l0.023,42.441l46.762-10.189c5.622-1.192,9.89-5.629,10.969-11.036        c-1.084-5.461-5.358-9.892-10.981-11.078L2413.045,2056.648z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000026856561977771265720000005572407135372988862_" filterUnits="userSpaceOnUse" x="2413.045" y="2056.638" width="58.093" height="71.529">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="2413.045" y="2056.638" width="58.093" height="71.529" id="SVGID_00000067917875633368503240000014883098216213909379_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000026856561977771265720000005572407135372988862_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000098211243531573174670000009253955048779805830_" filterUnits="userSpaceOnUse" x="2413.045" y="2056.638" width="58.093" height="71.529">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2413.045" y="2056.638" width="58.093" height="71.529" id="SVGID_00000067917875633368503240000014883098216213909379_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000098211243531573174670000009253955048779805830_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000154409791763127532820000015299952005443572638_" gradientUnits="userSpaceOnUse" x1="-1858.6865" y1="1697.0868" x2="-1800.6179" y2="1697.0868" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000067917875633368503240000014883098216213909379_);fill:url(#SVGID_00000154409791763127532820000015299952005443572638_);" d="           M2459.819,2066.776c5.623,1.185,9.896,5.617,10.981,11.076c0.217,0.919,0.325,1.893,0.325,2.867l0.013,23.247           c0.004,6.758-4.697,12.545-11.291,14.008l-46.762,10.192l-0.016-29.09l-0.023-42.439L2459.819,2066.776z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000137852339635954314320000013165197240011199142_" gradientUnits="userSpaceOnUse" x1="-1858.6865" y1="1697.0868" x2="-1800.6179" y2="1697.0868" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000137852339635954314320000013165197240011199142_);" d="M2459.819,2066.776         c5.623,1.185,9.896,5.617,10.981,11.076c0.217,0.919,0.325,1.893,0.325,2.867l0.013,23.247         c0.004,6.758-4.697,12.545-11.291,14.008l-46.762,10.192l-0.016-29.09l-0.023-42.439L2459.819,2066.776z"/>
						</g>
						<path style="fill:#6C708B;" d="M2404.907,2135.885l6.997-0.003c6.338-0.004,11.472-5.145,11.469-11.482l-0.036-64.006        c-0.003-6.337-5.144-11.472-11.481-11.468l-6.998,0.004L2404.907,2135.885z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000096038530037048730080000001336517825854636961_" filterUnits="userSpaceOnUse" x="2404.859" y="2048.935" width="18.515" height="86.96">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="2404.859" y="2048.935" width="18.515" height="86.96" id="SVGID_00000015340892128677637730000014583372321145869708_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000096038530037048730080000001336517825854636961_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000136393207508348767030000015027507360000602756_" filterUnits="userSpaceOnUse" x="2404.859" y="2048.935" width="18.515" height="86.96">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2404.859" y="2048.935" width="18.515" height="86.96" id="SVGID_00000015340892128677637730000014583372321145869708_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000136393207508348767030000015027507360000602756_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000098219029985529139530000017760983725110212031_" gradientUnits="userSpaceOnUse" x1="-1810.9116" y1="1697.0973" x2="-1792.4358" y2="1697.0973" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000015340892128677637730000014583372321145869708_);fill:url(#SVGID_00000098219029985529139530000017760983725110212031_);" d="           M2404.907,2135.896l6.997-0.004c6.338-0.004,11.472-5.144,11.469-11.482l-0.036-64.006           c-0.003-6.337-5.144-11.472-11.481-11.469l-6.998,0.004L2404.907,2135.896z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000023251005679212159090000007128128282202677143_" gradientUnits="userSpaceOnUse" x1="-1810.9116" y1="1697.0973" x2="-1792.4358" y2="1697.0973" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000023251005679212159090000007128128282202677143_);" d="M2404.907,2135.896l6.997-0.004         c6.338-0.004,11.472-5.144,11.469-11.482l-0.036-64.006c-0.003-6.337-5.144-11.472-11.481-11.469l-6.998,0.004         L2404.907,2135.896z"/>
						</g>
					</g>
					<g>
						<path style="fill:#FA2E0B;" d="M1010.494,2056.648l-0.039,71.53l-46.762-10.192c-6.596-1.463-11.295-7.25-11.292-14.008        l0.013-23.249c0-0.973,0.108-1.947,0.326-2.865c1.085-5.461,5.358-9.892,10.981-11.078L1010.494,2056.648z"/>
						<path style="fill:#F2C52C;" d="M1010.494,2056.648l-0.023,42.441l-46.762-10.189c-5.621-1.192-9.89-5.629-10.969-11.036        c1.085-5.461,5.358-9.892,10.981-11.078L1010.494,2056.648z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000132783380533062362380000010177425930178453382_" filterUnits="userSpaceOnUse" x="952.401" y="2056.638" width="58.093" height="71.529">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="952.401" y="2056.638" width="58.093" height="71.529" id="SVGID_00000104704050959487978450000017985794136400435633_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000132783380533062362380000010177425930178453382_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000152981546226976803360000010477449858262351802_" filterUnits="userSpaceOnUse" x="952.401" y="2056.638" width="58.093" height="71.529">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="952.401" y="2056.638" width="58.093" height="71.529" id="SVGID_00000104704050959487978450000017985794136400435633_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000152981546226976803360000010477449858262351802_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000119799583461996136630000012500475906826940049_" gradientUnits="userSpaceOnUse" x1="4785.5171" y1="1693.1002" x2="4843.5869" y2="1693.1002" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000104704050959487978450000017985794136400435633_);fill:url(#SVGID_00000119799583461996136630000012500475906826940049_);" d="           M963.721,2066.776c-5.623,1.185-9.896,5.617-10.981,11.076c-0.217,0.919-0.325,1.893-0.326,2.867l-0.013,23.247           c-0.003,6.758,4.696,12.545,11.292,14.008l46.762,10.192l0.016-29.09l0.023-42.439L963.721,2066.776z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000047060397995118701440000000371188903492581307_" gradientUnits="userSpaceOnUse" x1="4785.5171" y1="1693.1002" x2="4843.5869" y2="1693.1002" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000047060397995118701440000000371188903492581307_);" d="M963.721,2066.776         c-5.623,1.185-9.896,5.617-10.981,11.076c-0.217,0.919-0.325,1.893-0.326,2.867l-0.013,23.247         c-0.003,6.758,4.696,12.545,11.292,14.008l46.762,10.192l0.016-29.09l0.023-42.439L963.721,2066.776z"/>
						</g>
						<path style="fill:#6C708B;" d="M1018.632,2135.885l-6.997-0.003c-6.337-0.004-11.472-5.145-11.468-11.482l0.035-64.006        c0.003-6.337,5.144-11.472,11.481-11.468l6.998,0.004L1018.632,2135.885z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000168119266752113660100000005810755135740828852_" filterUnits="userSpaceOnUse" x="1000.166" y="2048.935" width="18.514" height="86.96">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="1000.166" y="2048.935" width="18.514" height="86.96" id="SVGID_00000036228548337809874400000013412658849155625088_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000168119266752113660100000005810755135740828852_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000000213277130121625130000002185420833117297287_" filterUnits="userSpaceOnUse" x="1000.166" y="2048.935" width="18.514" height="86.96">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1000.166" y="2048.935" width="18.514" height="86.96" id="SVGID_00000036228548337809874400000013412658849155625088_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000000213277130121625130000002185420833117297287_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000181052542835651701060000005248274668875974798_" gradientUnits="userSpaceOnUse" x1="4833.2925" y1="1693.1107" x2="4851.7681" y2="1693.1107" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000036228548337809874400000013412658849155625088_);fill:url(#SVGID_00000181052542835651701060000005248274668875974798_);" d="           M1018.632,2135.896l-6.997-0.004c-6.337-0.004-11.472-5.144-11.468-11.482l0.035-64.006           c0.003-6.337,5.144-11.472,11.481-11.469l6.998,0.004L1018.632,2135.896z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000090263441957022250060000011586076897221325450_" gradientUnits="userSpaceOnUse" x1="4833.2925" y1="1693.1107" x2="4851.7681" y2="1693.1107" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000090263441957022250060000011586076897221325450_);" d="M1018.632,2135.896l-6.997-0.004         c-6.337-0.004-11.472-5.144-11.468-11.482l0.035-64.006c0.003-6.337,5.144-11.472,11.481-11.469l6.998,0.004         L1018.632,2135.896z"/>
						</g>
					</g>
				</g>
				<g>
					<g>
						<path style="fill:#FA2E0B;" d="M2413.045,4339.18l0.04,71.529l46.762-10.191c6.595-1.463,11.295-7.25,11.291-14.008        l-0.013-23.249c0-0.973-0.109-1.947-0.325-2.865c-1.084-5.46-5.358-9.892-10.981-11.078L2413.045,4339.18z"/>
						<path style="fill:#F2C52C;" d="M2413.045,4339.18l0.023,42.44l46.762-10.189c5.622-1.193,9.89-5.628,10.969-11.036        c-1.084-5.46-5.358-9.892-10.981-11.078L2413.045,4339.18z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000101813909549282894720000007839107173947614623_" filterUnits="userSpaceOnUse" x="2413.045" y="4339.169" width="58.093" height="71.529">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="2413.045" y="4339.169" width="58.093" height="71.529" id="SVGID_00000087374100317446159030000010307025981129493124_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000101813909549282894720000007839107173947614623_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000159457072394928454160000001445990903879079591_" filterUnits="userSpaceOnUse" x="2413.045" y="4339.169" width="58.093" height="71.529">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2413.045" y="4339.169" width="58.093" height="71.529" id="SVGID_00000087374100317446159030000010307025981129493124_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000159457072394928454160000001445990903879079591_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000163045875071963925530000009361967688758997668_" gradientUnits="userSpaceOnUse" x1="-1857.317" y1="3979.6174" x2="-1799.2484" y2="3979.6174" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000087374100317446159030000010307025981129493124_);fill:url(#SVGID_00000163045875071963925530000009361967688758997668_);" d="           M2459.819,4349.307c5.623,1.185,9.896,5.618,10.981,11.077c0.217,0.919,0.325,1.893,0.325,2.867l0.013,23.248           c0.004,6.758-4.697,12.544-11.291,14.008l-46.762,10.192l-0.016-29.089l-0.023-42.44L2459.819,4349.307z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000076596519285776574680000010562177889277781402_" gradientUnits="userSpaceOnUse" x1="-1857.317" y1="3979.6174" x2="-1799.2484" y2="3979.6174" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000076596519285776574680000010562177889277781402_);" d="M2459.819,4349.307         c5.623,1.185,9.896,5.618,10.981,11.077c0.217,0.919,0.325,1.893,0.325,2.867l0.013,23.248         c0.004,6.758-4.697,12.544-11.291,14.008l-46.762,10.192l-0.016-29.089l-0.023-42.44L2459.819,4349.307z"/>
						</g>
						<path style="fill:#6C708B;" d="M2404.907,4418.417l6.997-0.004c6.338-0.004,11.472-5.145,11.469-11.481l-0.036-64.007        c-0.003-6.337-5.144-11.472-11.481-11.468l-6.998,0.004L2404.907,4418.417z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000068648741464449577240000006203484093644350399_" filterUnits="userSpaceOnUse" x="2404.859" y="4331.467" width="18.515" height="86.96">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="2404.859" y="4331.467" width="18.515" height="86.96" id="SVGID_00000167390129085939774080000003456827941005825442_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000068648741464449577240000006203484093644350399_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000023980152368013987830000013446611332304735876_" filterUnits="userSpaceOnUse" x="2404.859" y="4331.467" width="18.515" height="86.96">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2404.859" y="4331.467" width="18.515" height="86.96" id="SVGID_00000167390129085939774080000003456827941005825442_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000023980152368013987830000013446611332304735876_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000103958315960962912710000003883598092252286901_" gradientUnits="userSpaceOnUse" x1="-1809.5421" y1="3979.6279" x2="-1791.0663" y2="3979.6279" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000167390129085939774080000003456827941005825442_);fill:url(#SVGID_00000103958315960962912710000003883598092252286901_);" d="           M2404.907,4418.427l6.997-0.004c6.338-0.003,11.472-5.144,11.469-11.482l-0.036-64.005           c-0.003-6.338-5.144-11.473-11.481-11.469l-6.998,0.004L2404.907,4418.427z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000035527395786700322420000001552284884699870603_" gradientUnits="userSpaceOnUse" x1="-1809.5421" y1="3979.6279" x2="-1791.0663" y2="3979.6279" gradientTransform="matrix(-1 6.000000e-04 6.000000e-04 1 611.4271 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000035527395786700322420000001552284884699870603_);" d="M2404.907,4418.427l6.997-0.004         c6.338-0.003,11.472-5.144,11.469-11.482l-0.036-64.005c-0.003-6.338-5.144-11.473-11.481-11.469l-6.998,0.004         L2404.907,4418.427z"/>
						</g>
					</g>
					<g>
						<path style="fill:#FA2E0B;" d="M1010.494,4339.18l-0.039,71.529l-46.762-10.191c-6.596-1.463-11.295-7.25-11.292-14.008        l0.013-23.249c0-0.973,0.108-1.947,0.326-2.865c1.085-5.46,5.358-9.892,10.981-11.078L1010.494,4339.18z"/>
						<path style="fill:#F2C52C;" d="M1010.494,4339.18l-0.023,42.44l-46.762-10.189c-5.621-1.193-9.89-5.628-10.969-11.036        c1.085-5.46,5.358-9.892,10.981-11.078L1010.494,4339.18z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000055670252545384122980000009816087897720549042_" filterUnits="userSpaceOnUse" x="952.401" y="4339.169" width="58.093" height="71.529">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="952.401" y="4339.169" width="58.093" height="71.529" id="SVGID_00000023977193905571594290000003073659193999983496_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000055670252545384122980000009816087897720549042_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000105413823174220432990000018420631926281588628_" filterUnits="userSpaceOnUse" x="952.401" y="4339.169" width="58.093" height="71.529">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="952.401" y="4339.169" width="58.093" height="71.529" id="SVGID_00000023977193905571594290000003073659193999983496_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000105413823174220432990000018420631926281588628_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000125586293648037714640000013166520048719751305_" gradientUnits="userSpaceOnUse" x1="4786.8867" y1="3975.6309" x2="4844.9561" y2="3975.6309" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000023977193905571594290000003073659193999983496_);fill:url(#SVGID_00000125586293648037714640000013166520048719751305_);" d="           M963.721,4349.307c-5.623,1.185-9.896,5.618-10.981,11.077c-0.217,0.919-0.325,1.893-0.326,2.867l-0.013,23.248           c-0.003,6.758,4.696,12.544,11.292,14.008l46.762,10.192l0.016-29.089l0.023-42.44L963.721,4349.307z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000061462502279957228510000013994111850272357273_" gradientUnits="userSpaceOnUse" x1="4786.8867" y1="3975.6309" x2="4844.9561" y2="3975.6309" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000061462502279957228510000013994111850272357273_);" d="M963.721,4349.307         c-5.623,1.185-9.896,5.618-10.981,11.077c-0.217,0.919-0.325,1.893-0.326,2.867l-0.013,23.248         c-0.003,6.758,4.696,12.544,11.292,14.008l46.762,10.192l0.016-29.089l0.023-42.44L963.721,4349.307z"/>
						</g>
						<path style="fill:#6C708B;" d="M1018.632,4418.417l-6.997-0.004c-6.337-0.004-11.472-5.145-11.468-11.481l0.035-64.007        c0.003-6.337,5.144-11.472,11.481-11.468l6.998,0.004L1018.632,4418.417z"/>
						<g style="opacity:0.17;">
							<defs>
								
									<filter id="Adobe_OpacityMaskFilter_00000180324232129184154100000004718429277126456966_" filterUnits="userSpaceOnUse" x="1000.166" y="4331.467" width="18.514" height="86.96">
									<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
									<feBlend in="SourceGraphic" in2="back" mode="normal"/>
								</filter>
							</defs>
							
								<mask maskUnits="userSpaceOnUse" x="1000.166" y="4331.467" width="18.514" height="86.96" id="SVGID_00000150064207339928040950000016456952765051341738_">
								<g style="filter:url(#Adobe_OpacityMaskFilter_00000180324232129184154100000004718429277126456966_);">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000090273366378339429840000002344580106121077653_" filterUnits="userSpaceOnUse" x="1000.166" y="4331.467" width="18.514" height="86.96">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1000.166" y="4331.467" width="18.514" height="86.96" id="SVGID_00000150064207339928040950000016456952765051341738_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000090273366378339429840000002344580106121077653_);">
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000087413473212636444240000005974397627458829975_" gradientUnits="userSpaceOnUse" x1="4834.6616" y1="3975.6414" x2="4853.1377" y2="3975.6414" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
										<stop offset="0" style="stop-color:#FFFFFF"/>
										<stop offset="0.028" style="stop-color:#EEEEEE"/>
										<stop offset="0.1037" style="stop-color:#C4C4C4"/>
										<stop offset="0.1811" style="stop-color:#A2A2A2"/>
										<stop offset="0.2597" style="stop-color:#888888"/>
										<stop offset="0.3397" style="stop-color:#757575"/>
										<stop offset="0.4222" style="stop-color:#6A6A6A"/>
										<stop offset="0.5108" style="stop-color:#666666"/>
										<stop offset="0.5714" style="stop-color:#6F6F6F"/>
										<stop offset="0.6716" style="stop-color:#888888"/>
										<stop offset="0.7989" style="stop-color:#B1B1B1"/>
										<stop offset="0.9468" style="stop-color:#E9E9E9"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									
										<path style="mask:url(#SVGID_00000150064207339928040950000016456952765051341738_);fill:url(#SVGID_00000087413473212636444240000005974397627458829975_);" d="           M1018.632,4418.427l-6.997-0.004c-6.337-0.003-11.472-5.144-11.468-11.482l0.035-64.005           c0.003-6.338,5.144-11.473,11.481-11.469l6.998,0.004L1018.632,4418.427z"/>
								</g>
							</mask>
							
								<linearGradient id="SVGID_00000177456304914488308100000004333321749327670953_" gradientUnits="userSpaceOnUse" x1="4834.6616" y1="3975.6414" x2="4853.1377" y2="3975.6414" gradientTransform="matrix(1 6.000000e-04 -6.000000e-04 1 -3832.0945 396.3958)">
								<stop offset="0" style="stop-color:#FDFDFD"/>
								<stop offset="0.0102" style="stop-color:#F2F2F2"/>
								<stop offset="0.0685" style="stop-color:#BABABA"/>
								<stop offset="0.1286" style="stop-color:#898989"/>
								<stop offset="0.1892" style="stop-color:#5F5F5F"/>
								<stop offset="0.2504" style="stop-color:#3C3C3C"/>
								<stop offset="0.3125" style="stop-color:#222222"/>
								<stop offset="0.3757" style="stop-color:#0F0F0F"/>
								<stop offset="0.4408" style="stop-color:#040404"/>
								<stop offset="0.5108" style="stop-color:#000000"/>
								<stop offset="0.5523" style="stop-color:#090909"/>
								<stop offset="0.6209" style="stop-color:#222222"/>
								<stop offset="0.7081" style="stop-color:#4B4B4B"/>
								<stop offset="0.8105" style="stop-color:#848484"/>
								<stop offset="0.9244" style="stop-color:#CBCBCB"/>
								<stop offset="1" style="stop-color:#FFFFFF"/>
							</linearGradient>
							<path style="fill:url(#SVGID_00000177456304914488308100000004333321749327670953_);" d="M1018.632,4418.427l-6.997-0.004         c-6.337-0.003-11.472-5.144-11.468-11.482l0.035-64.005c0.003-6.338,5.144-11.473,11.481-11.469l6.998,0.004         L1018.632,4418.427z"/>
						</g>
					</g>
				</g>
				<g>
					<g>
						<g>
							<g>
								<g>
									<path style="fill:#F8AF0A;" d="M1295.743,4778.976v0.292c0,7.758-8.48,14.023-18.982,14.023h-34.762           c-10.503,0-19.032-6.265-19.032-14.023v-0.292c0-0.365,0-0.729,0.098-1.093c0.691-7.249,8.925-12.968,18.934-12.968h34.762           C1287.263,4764.915,1295.743,4771.217,1295.743,4778.976z"/>
									<path style="fill:#B30E0A;" d="M1454.954,4778.976v0.292c0,7.758-8.53,14.023-19.032,14.023h-113.552           c-10.503,0-19.033-6.265-19.033-14.023v-0.292c0-7.759,8.53-14.061,19.033-14.061h113.552           C1446.424,4764.915,1454.954,4771.217,1454.954,4778.976z"/>
								</g>
								<g style="opacity:0.4;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000018216944092172248060000006711017917265839541_" filterUnits="userSpaceOnUse" x="1222.967" y="4764.915" width="72.776" height="28.376">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1222.967" y="4764.915" width="72.776" height="28.376" id="SVGID_00000033349791685802252640000008110012387447213978_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000018216944092172248060000006711017917265839541_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000087381862407515360760000007819794219571278469_" filterUnits="userSpaceOnUse" x="1222.967" y="4764.915" width="72.776" height="28.376">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1222.967" y="4764.915" width="72.776" height="28.376" id="SVGID_00000033349791685802252640000008110012387447213978_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000087381862407515360760000007819794219571278469_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000044899674450169346880000016444952634694077598_" gradientUnits="userSpaceOnUse" x1="1222.9666" y1="4779.103" x2="1295.743" y2="4779.103">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0074" style="stop-color:#EEEEEE"/>
												<stop offset="0.0273" style="stop-color:#C4C4C4"/>
												<stop offset="0.0477" style="stop-color:#A2A2A2"/>
												<stop offset="0.0683" style="stop-color:#888888"/>
												<stop offset="0.0894" style="stop-color:#757575"/>
												<stop offset="0.1111" style="stop-color:#6A6A6A"/>
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000033349791685802252640000008110012387447213978_);fill:url(#SVGID_00000044899674450169346880000016444952634694077598_);" d="             M1295.743,4778.976v0.292c0,7.758-8.48,14.023-18.982,14.023h-34.762c-10.503,0-19.032-6.265-19.032-14.023v-0.292             c0-0.365,0-0.729,0.098-1.093c0.691-7.249,8.925-12.968,18.934-12.968h34.762             C1287.263,4764.915,1295.743,4771.217,1295.743,4778.976z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000088118686066629532770000003252749516321439104_" gradientUnits="userSpaceOnUse" x1="1222.9666" y1="4779.103" x2="1295.743" y2="4779.103">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0027" style="stop-color:#F2F2F2"/>
										<stop offset="0.018" style="stop-color:#BABABA"/>
										<stop offset="0.0338" style="stop-color:#898989"/>
										<stop offset="0.0498" style="stop-color:#5F5F5F"/>
										<stop offset="0.0659" style="stop-color:#3C3C3C"/>
										<stop offset="0.0822" style="stop-color:#222222"/>
										<stop offset="0.0989" style="stop-color:#0F0F0F"/>
										<stop offset="0.116" style="stop-color:#040404"/>
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000088118686066629532770000003252749516321439104_);" d="M1295.743,4778.976v0.292           c0,7.758-8.48,14.023-18.982,14.023h-34.762c-10.503,0-19.032-6.265-19.032-14.023v-0.292c0-0.365,0-0.729,0.098-1.093           c0.691-7.249,8.925-12.968,18.934-12.968h34.762C1287.263,4764.915,1295.743,4771.217,1295.743,4778.976z"/>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000102531458679966746510000009792234816850548907_" filterUnits="userSpaceOnUse" x="1303.336" y="4764.915" width="151.618" height="28.376">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1303.336" y="4764.915" width="151.618" height="28.376" id="SVGID_00000091017651930573298960000012936867520135585183_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000102531458679966746510000009792234816850548907_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000101822138216293525240000005998448166811028157_" filterUnits="userSpaceOnUse" x="1303.336" y="4764.915" width="151.618" height="28.376">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1303.336" y="4764.915" width="151.618" height="28.376" id="SVGID_00000091017651930573298960000012936867520135585183_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000101822138216293525240000005998448166811028157_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000154394958077630509240000015968477905406595768_" gradientUnits="userSpaceOnUse" x1="1303.3361" y1="4779.103" x2="1454.9539" y2="4779.103">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0074" style="stop-color:#EEEEEE"/>
												<stop offset="0.0273" style="stop-color:#C4C4C4"/>
												<stop offset="0.0477" style="stop-color:#A2A2A2"/>
												<stop offset="0.0683" style="stop-color:#888888"/>
												<stop offset="0.0894" style="stop-color:#757575"/>
												<stop offset="0.1111" style="stop-color:#6A6A6A"/>
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000091017651930573298960000012936867520135585183_);fill:url(#SVGID_00000154394958077630509240000015968477905406595768_);" d="             M1454.954,4778.976v0.292c0,7.758-8.53,14.023-19.032,14.023h-113.552c-10.503,0-19.033-6.265-19.033-14.023v-0.292             c0-7.759,8.53-14.061,19.033-14.061h113.552C1446.424,4764.915,1454.954,4771.217,1454.954,4778.976z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000027580649231162921460000009242641279679624634_" gradientUnits="userSpaceOnUse" x1="1303.3361" y1="4779.103" x2="1454.9539" y2="4779.103">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0027" style="stop-color:#F2F2F2"/>
										<stop offset="0.018" style="stop-color:#BABABA"/>
										<stop offset="0.0338" style="stop-color:#898989"/>
										<stop offset="0.0498" style="stop-color:#5F5F5F"/>
										<stop offset="0.0659" style="stop-color:#3C3C3C"/>
										<stop offset="0.0822" style="stop-color:#222222"/>
										<stop offset="0.0989" style="stop-color:#0F0F0F"/>
										<stop offset="0.116" style="stop-color:#040404"/>
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000027580649231162921460000009242641279679624634_);" d="M1454.954,4778.976v0.292           c0,7.758-8.53,14.023-19.032,14.023h-113.552c-10.503,0-19.033-6.265-19.033-14.023v-0.292           c0-7.759,8.53-14.061,19.033-14.061h113.552C1446.424,4764.915,1454.954,4771.217,1454.954,4778.976z"/>
								</g>
							</g>
							<g>
								<g>
									<path style="fill:#F8AF0A;" d="M2200.519,4778.976v0.292c0,7.758-8.48,14.023-18.982,14.023h-34.762           c-10.503,0-18.983-6.265-18.983-14.023v-0.292c0-7.759,8.48-14.061,18.983-14.061h34.762c9.91,0,17.997,5.61,18.883,12.712           C2200.47,4778.064,2200.519,4778.539,2200.519,4778.976z"/>
									<path style="fill:#B30E0A;" d="M2120.2,4778.976v0.292c0,7.758-8.531,14.023-19.032,14.023h-113.554           c-10.502,0-19.032-6.265-19.032-14.023v-0.292c0-7.759,8.53-14.061,19.032-14.061h113.554           C2111.668,4764.915,2120.2,4771.217,2120.2,4778.976z"/>
								</g>
								<g style="opacity:0.4;">
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000052085496772462488910000017554253856736762803_" filterUnits="userSpaceOnUse" x="2127.792" y="4764.915" width="72.727" height="28.376">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="2127.792" y="4764.915" width="72.727" height="28.376" id="SVGID_00000143593834665411478380000015427798689143562392_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000052085496772462488910000017554253856736762803_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000166673977252613325490000002265041485174351022_" filterUnits="userSpaceOnUse" x="2127.792" y="4764.915" width="72.727" height="28.376">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2127.792" y="4764.915" width="72.727" height="28.376" id="SVGID_00000143593834665411478380000015427798689143562392_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000166673977252613325490000002265041485174351022_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000175294874841951671070000000059898461355396751_" gradientUnits="userSpaceOnUse" x1="2127.792" y1="4779.103" x2="2200.5193" y2="4779.103">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0074" style="stop-color:#EEEEEE"/>
												<stop offset="0.0273" style="stop-color:#C4C4C4"/>
												<stop offset="0.0477" style="stop-color:#A2A2A2"/>
												<stop offset="0.0683" style="stop-color:#888888"/>
												<stop offset="0.0894" style="stop-color:#757575"/>
												<stop offset="0.1111" style="stop-color:#6A6A6A"/>
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000143593834665411478380000015427798689143562392_);fill:url(#SVGID_00000175294874841951671070000000059898461355396751_);" d="             M2200.519,4778.976v0.292c0,7.758-8.48,14.023-18.982,14.023h-34.762c-10.503,0-18.983-6.265-18.983-14.023v-0.292             c0-7.759,8.48-14.061,18.983-14.061h34.762c9.91,0,17.997,5.61,18.883,12.712             C2200.47,4778.064,2200.519,4778.539,2200.519,4778.976z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000100363103749638074070000014418871962034777268_" gradientUnits="userSpaceOnUse" x1="2127.792" y1="4779.103" x2="2200.5193" y2="4779.103">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0027" style="stop-color:#F2F2F2"/>
										<stop offset="0.018" style="stop-color:#BABABA"/>
										<stop offset="0.0338" style="stop-color:#898989"/>
										<stop offset="0.0498" style="stop-color:#5F5F5F"/>
										<stop offset="0.0659" style="stop-color:#3C3C3C"/>
										<stop offset="0.0822" style="stop-color:#222222"/>
										<stop offset="0.0989" style="stop-color:#0F0F0F"/>
										<stop offset="0.116" style="stop-color:#040404"/>
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000100363103749638074070000014418871962034777268_);" d="M2200.519,4778.976v0.292           c0,7.758-8.48,14.023-18.982,14.023h-34.762c-10.503,0-18.983-6.265-18.983-14.023v-0.292           c0-7.759,8.48-14.061,18.983-14.061h34.762c9.91,0,17.997,5.61,18.883,12.712           C2200.47,4778.064,2200.519,4778.539,2200.519,4778.976z"/>
									<defs>
										
											<filter id="Adobe_OpacityMaskFilter_00000146474821869962489610000000309943654070093953_" filterUnits="userSpaceOnUse" x="1968.582" y="4764.915" width="151.618" height="28.376">
											<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
											<feBlend in="SourceGraphic" in2="back" mode="normal"/>
										</filter>
									</defs>
									
										<mask maskUnits="userSpaceOnUse" x="1968.582" y="4764.915" width="151.618" height="28.376" id="SVGID_00000118383130259471039200000013529972531483863443_">
										<g style="filter:url(#Adobe_OpacityMaskFilter_00000146474821869962489610000000309943654070093953_);">
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000169535136229092705150000015680961974033758377_" filterUnits="userSpaceOnUse" x="1968.582" y="4764.915" width="151.618" height="28.376">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1968.582" y="4764.915" width="151.618" height="28.376" id="SVGID_00000118383130259471039200000013529972531483863443_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000169535136229092705150000015680961974033758377_);">
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000108276204845186657730000007654776570355124403_" gradientUnits="userSpaceOnUse" x1="1968.5819" y1="4779.103" x2="2120.1997" y2="4779.103">
												<stop offset="0" style="stop-color:#FFFFFF"/>
												<stop offset="0.0074" style="stop-color:#EEEEEE"/>
												<stop offset="0.0273" style="stop-color:#C4C4C4"/>
												<stop offset="0.0477" style="stop-color:#A2A2A2"/>
												<stop offset="0.0683" style="stop-color:#888888"/>
												<stop offset="0.0894" style="stop-color:#757575"/>
												<stop offset="0.1111" style="stop-color:#6A6A6A"/>
												<stop offset="0.1344" style="stop-color:#666666"/>
												<stop offset="0.3495" style="stop-color:#666666"/>
												<stop offset="0.4821" style="stop-color:#5D5D5D"/>
												<stop offset="0.7004" style="stop-color:#444444"/>
												<stop offset="0.8226" style="stop-color:#333333"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<path style="mask:url(#SVGID_00000118383130259471039200000013529972531483863443_);fill:url(#SVGID_00000108276204845186657730000007654776570355124403_);" d="             M2120.2,4778.976v0.292c0,7.758-8.531,14.023-19.032,14.023h-113.554c-10.502,0-19.032-6.265-19.032-14.023v-0.292             c0-7.759,8.53-14.061,19.032-14.061h113.554C2111.668,4764.915,2120.2,4771.217,2120.2,4778.976z"/>
										</g>
									</mask>
									
										<linearGradient id="SVGID_00000141452556091494466810000009673529675946512788_" gradientUnits="userSpaceOnUse" x1="1968.5819" y1="4779.103" x2="2120.1997" y2="4779.103">
										<stop offset="0" style="stop-color:#FDFDFD"/>
										<stop offset="0.0027" style="stop-color:#F2F2F2"/>
										<stop offset="0.018" style="stop-color:#BABABA"/>
										<stop offset="0.0338" style="stop-color:#898989"/>
										<stop offset="0.0498" style="stop-color:#5F5F5F"/>
										<stop offset="0.0659" style="stop-color:#3C3C3C"/>
										<stop offset="0.0822" style="stop-color:#222222"/>
										<stop offset="0.0989" style="stop-color:#0F0F0F"/>
										<stop offset="0.116" style="stop-color:#040404"/>
										<stop offset="0.1344" style="stop-color:#000000"/>
										<stop offset="0.1553" style="stop-color:#090909"/>
										<stop offset="0.1899" style="stop-color:#222222"/>
										<stop offset="0.2339" style="stop-color:#4A4B4B"/>
										<stop offset="0.2855" style="stop-color:#828384"/>
										<stop offset="0.3429" style="stop-color:#C9CACB"/>
										<stop offset="0.3495" style="stop-color:#D1D3D4"/>
										<stop offset="0.4084" style="stop-color:#C8CACB"/>
										<stop offset="0.5058" style="stop-color:#B0B1B2"/>
										<stop offset="0.6294" style="stop-color:#888889"/>
										<stop offset="0.7733" style="stop-color:#525051"/>
										<stop offset="0.8226" style="stop-color:#3E3B3C"/>
										<stop offset="1" style="stop-color:#FFFFFF"/>
									</linearGradient>
									<path style="fill:url(#SVGID_00000141452556091494466810000009673529675946512788_);" d="M2120.2,4778.976v0.292           c0,7.758-8.531,14.023-19.032,14.023h-113.554c-10.502,0-19.032-6.265-19.032-14.023v-0.292           c0-7.759,8.53-14.061,19.032-14.061h113.554C2111.668,4764.915,2120.2,4771.217,2120.2,4778.976z"/>
								</g>
							</g>
						</g>
						<g>
							<g>
								<g>
									<g style="opacity:0.2;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000119839488943097483370000002690198339083475354_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566" id="SVGID_00000005976835322790678200000011175460955901639316_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000119839488943097483370000002690198339083475354_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000054948420870345417760000016934435854866967980_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566" id="SVGID_00000005976835322790678200000011175460955901639316_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000054948420870345417760000016934435854866967980_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000014594025812428111940000006404849291014690448_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4584.9858" x2="1005.9676" y2="4584.9858">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000005976835322790678200000011175460955901639316_);fill:url(#SVGID_00000014594025812428111940000006404849291014690448_);" points="              1005.968,4578.703 1005.968,4591.269 990.98,4591.269 977.42,4580.815 977.42,4580.779 974.708,4578.703                           "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000086657647723064691570000001689292527456765353_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4584.9858" x2="1005.9676" y2="4584.9858">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000086657647723064691570000001689292527456765353_);" points="1005.968,4578.703             1005.968,4591.269 990.98,4591.269 977.42,4580.815 977.42,4580.779 974.708,4578.703           "/>
									</g>
									<rect x="974.708" y="4578.703" style="fill:#721717;" width="31.259" height="54.458"/>
									<rect x="977.407" y="4580.796" style="fill:#B00A09;" width="28.601" height="50.248"/>
									<rect x="990.975" y="4591.264" style="fill:#E30B09;" width="15.033" height="29.311"/>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000149362558962513592200000000506493859743088781_" filterUnits="userSpaceOnUse" x="977.407" y="4580.796" width="28.601" height="50.248">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="977.407" y="4580.796" width="28.601" height="50.248" id="SVGID_00000053532316562400311110000005557914285675382187_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000149362558962513592200000000506493859743088781_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000124851035018241232620000003962026011255439765_" filterUnits="userSpaceOnUse" x="977.407" y="4580.796" width="28.601" height="50.248">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="977.407" y="4580.796" width="28.601" height="50.248" id="SVGID_00000053532316562400311110000005557914285675382187_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000124851035018241232620000003962026011255439765_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000080888185545957112390000003083493992673554568_" gradientUnits="userSpaceOnUse" x1="977.4073" y1="4605.9199" x2="1006.0082" y2="4605.9199">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<rect x="977.407" y="4580.796" style="mask:url(#SVGID_00000053532316562400311110000005557914285675382187_);fill:url(#SVGID_00000080888185545957112390000003083493992673554568_);" width="28.601" height="50.248"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000111189423384921313430000000680797469013520001_" gradientUnits="userSpaceOnUse" x1="977.4073" y1="4605.9199" x2="1006.0082" y2="4605.9199">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										
											<rect x="977.407" y="4580.796" style="fill:url(#SVGID_00000111189423384921313430000000680797469013520001_);" width="28.601" height="50.248"/>
									</g>
									<g style="opacity:0.2;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000011018118942033295300000017234394072046868656_" filterUnits="userSpaceOnUse" x="990.975" y="4591.264" width="15.033" height="29.311">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="990.975" y="4591.264" width="15.033" height="29.311" id="SVGID_00000042725515910167291370000016053889381827006629_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000011018118942033295300000017234394072046868656_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000096017197221101475010000005997133602671873448_" filterUnits="userSpaceOnUse" x="990.975" y="4591.264" width="15.033" height="29.311">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="990.975" y="4591.264" width="15.033" height="29.311" id="SVGID_00000042725515910167291370000016053889381827006629_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000096017197221101475010000005997133602671873448_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000100370713808680558220000003547089778599954321_" gradientUnits="userSpaceOnUse" x1="990.9753" y1="4605.9199" x2="1006.0082" y2="4605.9199">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<rect x="990.975" y="4591.264" style="mask:url(#SVGID_00000042725515910167291370000016053889381827006629_);fill:url(#SVGID_00000100370713808680558220000003547089778599954321_);" width="15.033" height="29.311"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000063627381073533737540000000527833047980221339_" gradientUnits="userSpaceOnUse" x1="990.9753" y1="4605.9199" x2="1006.0082" y2="4605.9199">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										
											<rect x="990.975" y="4591.264" style="fill:url(#SVGID_00000063627381073533737540000000527833047980221339_);" width="15.033" height="29.311"/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000086681269737521499010000000391843843082246302_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="16.272" height="54.458">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="16.272" height="54.458" id="SVGID_00000129174903836844649200000017881095329194554295_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000086681269737521499010000000391843843082246302_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000028325055006584168200000017318634335246304664_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="16.272" height="54.458">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="16.272" height="54.458" id="SVGID_00000129174903836844649200000017881095329194554295_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000028325055006584168200000017318634335246304664_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000067215803482699711090000009928251199731132558_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4605.9312" x2="990.98" y2="4605.9312">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000129174903836844649200000017881095329194554295_);fill:url(#SVGID_00000067215803482699711090000009928251199731132558_);" points="              990.98,4591.269 990.98,4620.593 977.42,4631.047 974.708,4633.16 974.708,4578.703 977.42,4580.779 977.42,4580.815                           "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000022561101320900674670000011907095050907003545_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4605.9312" x2="990.98" y2="4605.9312">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000022561101320900674670000011907095050907003545_);" points="990.98,4591.269             990.98,4620.593 977.42,4631.047 974.708,4633.16 974.708,4578.703 977.42,4580.779 977.42,4580.815           "/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000045591095737032275940000000622270139189073851_" filterUnits="userSpaceOnUse" x="974.708" y="4620.593" width="31.259" height="12.567">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="974.708" y="4620.593" width="31.259" height="12.567" id="SVGID_00000059273817217494054070000014743459875356752270_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000045591095737032275940000000622270139189073851_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000070079435783998409420000001434042595960421032_" filterUnits="userSpaceOnUse" x="974.708" y="4620.593" width="31.259" height="12.567">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="974.708" y="4620.593" width="31.259" height="12.567" id="SVGID_00000059273817217494054070000014743459875356752270_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000070079435783998409420000001434042595960421032_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000180368205370586033140000009606673374813942200_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4626.8765" x2="1005.9676" y2="4626.8765">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000059273817217494054070000014743459875356752270_);fill:url(#SVGID_00000180368205370586033140000009606673374813942200_);" points="              1005.968,4620.593 1005.968,4633.16 974.708,4633.16 977.42,4631.047 990.98,4620.593             "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000093869139410276400720000010105772030510597281_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4626.8765" x2="1005.9676" y2="4626.8765">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000093869139410276400720000010105772030510597281_);" points="1005.968,4620.593             1005.968,4633.16 974.708,4633.16 977.42,4631.047 990.98,4620.593           "/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000132767352284137908250000013621643267714844341_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566" id="SVGID_00000048490966911025387820000010140495380379693965_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000132767352284137908250000013621643267714844341_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000106142198298163048880000016826334956987795388_" filterUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="974.708" y="4578.703" width="31.259" height="12.566" id="SVGID_00000048490966911025387820000010140495380379693965_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000106142198298163048880000016826334956987795388_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000093898114224308708680000011844147469636729785_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4584.9858" x2="1005.9676" y2="4584.9858">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000048490966911025387820000010140495380379693965_);fill:url(#SVGID_00000093898114224308708680000011844147469636729785_);" points="              1005.968,4578.703 1005.968,4591.269 990.98,4591.269 977.42,4580.815 977.42,4580.779 974.708,4578.703                           "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000015328611828372531030000008569240365581215132_" gradientUnits="userSpaceOnUse" x1="974.7083" y1="4584.9858" x2="1005.9676" y2="4584.9858">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000015328611828372531030000008569240365581215132_);" points="1005.968,4578.703             1005.968,4591.269 990.98,4591.269 977.42,4580.815 977.42,4580.779 974.708,4578.703           "/>
									</g>
								</g>
								<g>
									<g style="opacity:0.2;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000062156413506637576470000001683842385479160491_" filterUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556" id="SVGID_00000161611204855664391060000002151863820654423954_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000062156413506637576470000001683842385479160491_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000050626055145955722170000004792731619074342583_" filterUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556" id="SVGID_00000161611204855664391060000002151863820654423954_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000050626055145955722170000004792731619074342583_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000032646131680363755500000014693289768381929653_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4626.8667" x2="2448.8308" y2="4626.8667">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000161611204855664391060000002151863820654423954_);fill:url(#SVGID_00000032646131680363755500000014693289768381929653_);" points="              2448.831,4633.145 2417.493,4633.145 2417.493,4620.589 2432.559,4620.589 2446.096,4631.034             "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000030448495915423693050000015586352205712336555_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4626.8667" x2="2448.8308" y2="4626.8667">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000030448495915423693050000015586352205712336555_);" points="2448.831,4633.145             2417.493,4633.145 2417.493,4620.589 2432.559,4620.589 2446.096,4631.034           "/>
									</g>
									<rect x="2417.493" y="4578.704" style="fill:#721717;" width="31.337" height="54.44"/>
									<rect x="2417.508" y="4580.796" style="fill:#B00A09;" width="28.601" height="50.248"/>
									<rect x="2417.508" y="4591.264" style="fill:#E30B09;" width="15.033" height="29.312"/>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000015345326175019052260000006778819642652764849_" filterUnits="userSpaceOnUse" x="2417.508" y="4580.796" width="28.601" height="50.248">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2417.508" y="4580.796" width="28.601" height="50.248" id="SVGID_00000155832963128386247000000003012345072790519708_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000015345326175019052260000006778819642652764849_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000165204568157430112790000013997452172315846297_" filterUnits="userSpaceOnUse" x="2417.508" y="4580.796" width="28.601" height="50.248">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2417.508" y="4580.796" width="28.601" height="50.248" id="SVGID_00000155832963128386247000000003012345072790519708_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000165204568157430112790000013997452172315846297_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000178901777692538843280000006591896307276769164_" gradientUnits="userSpaceOnUse" x1="197.1826" y1="4771.228" x2="225.7836" y2="4771.228" gradientTransform="matrix(-1 0 0 -1 2643.2915 9377.1475)">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<rect x="2417.508" y="4580.796" style="mask:url(#SVGID_00000155832963128386247000000003012345072790519708_);fill:url(#SVGID_00000178901777692538843280000006591896307276769164_);" width="28.601" height="50.248"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000018959217354595132040000013855526961307987852_" gradientUnits="userSpaceOnUse" x1="197.1826" y1="4771.228" x2="225.7836" y2="4771.228" gradientTransform="matrix(-1 0 0 -1 2643.2915 9377.1475)">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										
											<rect x="2417.508" y="4580.796" style="fill:url(#SVGID_00000018959217354595132040000013855526961307987852_);" width="28.601" height="50.248"/>
									</g>
									<g style="opacity:0.2;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000082337458025711571660000006589884972721560721_" filterUnits="userSpaceOnUse" x="2417.508" y="4591.264" width="15.033" height="29.312">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2417.508" y="4591.264" width="15.033" height="29.312" id="SVGID_00000014601067095719133280000012445055736306450110_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000082337458025711571660000006589884972721560721_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000098212567341959482110000009363611078678702482_" filterUnits="userSpaceOnUse" x="2417.508" y="4591.264" width="15.033" height="29.312">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2417.508" y="4591.264" width="15.033" height="29.312" id="SVGID_00000014601067095719133280000012445055736306450110_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000098212567341959482110000009363611078678702482_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000104663675690000086510000014390003148947119490_" gradientUnits="userSpaceOnUse" x1="210.7505" y1="4771.2275" x2="225.7836" y2="4771.2275" gradientTransform="matrix(-1 0 0 -1 2643.2915 9377.1475)">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<rect x="2417.508" y="4591.264" style="mask:url(#SVGID_00000014601067095719133280000012445055736306450110_);fill:url(#SVGID_00000104663675690000086510000014390003148947119490_);" width="15.033" height="29.312"/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000085228919056618932810000018127689746320122783_" gradientUnits="userSpaceOnUse" x1="210.7505" y1="4771.2275" x2="225.7836" y2="4771.2275" gradientTransform="matrix(-1 0 0 -1 2643.2915 9377.1475)">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										
											<rect x="2417.508" y="4591.264" style="fill:url(#SVGID_00000085228919056618932810000018127689746320122783_);" width="15.033" height="29.312"/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000144327205642384926500000009119134146321331616_" filterUnits="userSpaceOnUse" x="2432.559" y="4578.704" width="16.272" height="54.44">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2432.559" y="4578.704" width="16.272" height="54.44" id="SVGID_00000005967816467907553450000007281950361368370615_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000144327205642384926500000009119134146321331616_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000050653760924558174200000002389849197470979001_" filterUnits="userSpaceOnUse" x="2432.559" y="4578.704" width="16.272" height="54.44">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2432.559" y="4578.704" width="16.272" height="54.44" id="SVGID_00000005967816467907553450000007281950361368370615_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000050653760924558174200000002389849197470979001_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000161619426949502361600000016103746909217138352_" gradientUnits="userSpaceOnUse" x1="2432.5588" y1="4605.9243" x2="2448.8308" y2="4605.9243">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000005967816467907553450000007281950361368370615_);fill:url(#SVGID_00000161619426949502361600000016103746909217138352_);" points="              2448.831,4578.704 2448.831,4633.145 2446.096,4631.034 2432.559,4620.589 2432.559,4591.259 2446.096,4580.815                           "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000161590694090891845710000012184987357390299025_" gradientUnits="userSpaceOnUse" x1="2432.5588" y1="4605.9243" x2="2448.8308" y2="4605.9243">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000161590694090891845710000012184987357390299025_);" points="2448.831,4578.704             2448.831,4633.145 2446.096,4631.034 2432.559,4620.589 2432.559,4591.259 2446.096,4580.815           "/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000065062474818732463010000015251055826463465110_" filterUnits="userSpaceOnUse" x="2417.493" y="4578.704" width="31.337" height="12.555">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2417.493" y="4578.704" width="31.337" height="12.555" id="SVGID_00000173878728516036585990000014216062258507550857_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000065062474818732463010000015251055826463465110_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000093171565743026044070000007499804016834009250_" filterUnits="userSpaceOnUse" x="2417.493" y="4578.704" width="31.337" height="12.555">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2417.493" y="4578.704" width="31.337" height="12.555" id="SVGID_00000173878728516036585990000014216062258507550857_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000093171565743026044070000007499804016834009250_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000055708180959115216140000003408485732723349158_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4584.9814" x2="2448.8308" y2="4584.9814">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000173878728516036585990000014216062258507550857_);fill:url(#SVGID_00000055708180959115216140000003408485732723349158_);" points="              2448.831,4578.704 2446.096,4580.815 2432.559,4591.259 2417.493,4591.259 2417.493,4578.704             "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000029021898792033584880000015902094987283862183_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4584.9814" x2="2448.8308" y2="4584.9814">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000029021898792033584880000015902094987283862183_);" points="2448.831,4578.704             2446.096,4580.815 2432.559,4591.259 2417.493,4591.259 2417.493,4578.704           "/>
									</g>
									<g style="opacity:0.13;">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000140014041326575642180000004531582678154008242_" filterUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556" id="SVGID_00000017501731681080400960000013679744092979278247_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000140014041326575642180000004531582678154008242_);">
												<defs>
													
														<filter id="Adobe_OpacityMaskFilter_00000129906721807935389050000000725730915842404226_" filterUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556">
														<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
														<feBlend in="SourceGraphic" in2="back" mode="normal"/>
													</filter>
												</defs>
												
													<mask maskUnits="userSpaceOnUse" x="2417.493" y="4620.589" width="31.337" height="12.556" id="SVGID_00000017501731681080400960000013679744092979278247_">
													<g style="filter:url(#Adobe_OpacityMaskFilter_00000129906721807935389050000000725730915842404226_);">
													</g>
												</mask>
												
													<linearGradient id="SVGID_00000090287388808747810210000013791474582783855501_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4626.8667" x2="2448.8308" y2="4626.8667">
													<stop offset="0" style="stop-color:#FFFFFF"/>
													<stop offset="0.028" style="stop-color:#EEEEEE"/>
													<stop offset="0.1037" style="stop-color:#C4C4C4"/>
													<stop offset="0.1811" style="stop-color:#A2A2A2"/>
													<stop offset="0.2597" style="stop-color:#888888"/>
													<stop offset="0.3397" style="stop-color:#757575"/>
													<stop offset="0.4222" style="stop-color:#6A6A6A"/>
													<stop offset="0.5108" style="stop-color:#666666"/>
													<stop offset="0.5714" style="stop-color:#6F6F6F"/>
													<stop offset="0.6716" style="stop-color:#888888"/>
													<stop offset="0.7989" style="stop-color:#B1B1B1"/>
													<stop offset="0.9468" style="stop-color:#E9E9E9"/>
													<stop offset="1" style="stop-color:#FFFFFF"/>
												</linearGradient>
												
													<polygon style="mask:url(#SVGID_00000017501731681080400960000013679744092979278247_);fill:url(#SVGID_00000090287388808747810210000013791474582783855501_);" points="              2448.831,4633.145 2417.493,4633.145 2417.493,4620.589 2432.559,4620.589 2446.096,4631.034             "/>
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000037689743800905284790000012423738569873041566_" gradientUnits="userSpaceOnUse" x1="2417.4934" y1="4626.8667" x2="2448.8308" y2="4626.8667">
											<stop offset="0" style="stop-color:#FDFDFD"/>
											<stop offset="0.0102" style="stop-color:#F2F2F2"/>
											<stop offset="0.0685" style="stop-color:#BABABA"/>
											<stop offset="0.1286" style="stop-color:#898989"/>
											<stop offset="0.1892" style="stop-color:#5F5F5F"/>
											<stop offset="0.2504" style="stop-color:#3C3C3C"/>
											<stop offset="0.3125" style="stop-color:#222222"/>
											<stop offset="0.3757" style="stop-color:#0F0F0F"/>
											<stop offset="0.4408" style="stop-color:#040404"/>
											<stop offset="0.5108" style="stop-color:#000000"/>
											<stop offset="0.5523" style="stop-color:#090909"/>
											<stop offset="0.6209" style="stop-color:#222222"/>
											<stop offset="0.7081" style="stop-color:#4B4B4B"/>
											<stop offset="0.8105" style="stop-color:#848484"/>
											<stop offset="0.9244" style="stop-color:#CBCBCB"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										<polygon style="fill:url(#SVGID_00000037689743800905284790000012423738569873041566_);" points="2448.831,4633.145             2417.493,4633.145 2417.493,4620.589 2432.559,4620.589 2446.096,4631.034           "/>
									</g>
								</g>
							</g>
							<g>
								<path style="fill:#050505;" d="M2424.274,4620.593c-0.543,3.497-1.183,6.958-2.022,10.455          c-0.099,0.692-0.295,1.42-0.542,2.113c-3.303,13.186-8.728,26.044-16.272,38.394c-3.403,5.5-7.15,10.892-11.341,16.137          l-1.134,1.347c-22.336,27.976-54.977,50.524-94.176,66.042c-29.584,11.766-62.914,19.525-98.367,22.548          c-11.29,0.983-22.779,1.494-34.465,1.494h-911.481c-10.602,0-21.104-0.401-31.409-1.238          c-83.723-6.558-156.005-39.159-195.796-88.917l-1.133-1.384c-13.559-16.974-22.829-35.37-27.613-54.421          c-0.245-0.729-0.393-1.421-0.492-2.113c-0.839-3.497-1.529-6.958-2.022-10.455c-1.479-9.653-1.726-19.488-0.938-29.324          c0.247-3.132,0.642-6.192,1.134-9.325l0.197-1.166l0.297-2.076l13.806-87.861l7.445-47.354          c-1.973,12.495-1.924,24.989,0.148,37.191c0.738,4.517,1.725,9.034,3.056,13.515c0.346,1.275,0.741,2.586,1.183,3.897v0.037          c3.649,11.147,8.975,21.965,15.976,32.347c0.74,1.129,1.528,2.258,2.367,3.388c0.198,0.328,0.444,0.655,0.74,0.947v0.036          c0.739,1.166,1.627,2.259,2.564,3.387v0.037c1.085,1.494,2.268,2.95,3.501,4.444l1.085,1.312          c3.253,3.934,6.803,7.723,10.501,11.401c0.148,0.183,0.297,0.328,0.444,0.474c45.609,45.387,123.02,72.707,209.405,72.707          h882.539c27.218,0,53.498-2.732,78.201-7.868c60.351-12.495,111.135-39.486,142.003-76.642l1.083-1.312          c1.135-1.348,2.22-2.695,3.255-4.043v-0.037c0.838-1.129,1.727-2.222,2.564-3.351c0.198-0.292,0.395-0.583,0.641-0.911          c0.789-1.129,1.578-2.259,2.366-3.388c5.128-7.541,9.369-15.299,12.672-23.277c0.741-1.53,1.332-3.132,1.922-4.662          c0,0,0,0,0-0.037c0.543-1.494,1.135-3.024,1.578-4.517v-0.037c0.542-1.603,1.084-3.206,1.479-4.808          c4.684-16.392,5.769-33.294,3.058-50.305l3.155,20.035l7.593,48.229l10.551,67.025l0.297,2.076l0.198,1.056          c0.493,3.169,0.887,6.265,1.183,9.434C2425.999,4601.104,2425.704,4610.94,2424.274,4620.593z"/>
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000139294583142036795840000012062928870689737347_" filterUnits="userSpaceOnUse" x="994.631" y="4443.414" width="1431.02" height="335.707">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="994.631" y="4443.414" width="1431.02" height="335.707" id="SVGID_00000178911166895505624210000004909463419227699897_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000139294583142036795840000012062928870689737347_);">
										<defs>
											
												<filter id="Adobe_OpacityMaskFilter_00000107581894359260270560000014028769201051540654_" filterUnits="userSpaceOnUse" x="994.631" y="4443.414" width="1431.02" height="335.707">
												<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
												<feBlend in="SourceGraphic" in2="back" mode="normal"/>
											</filter>
										</defs>
										
											<mask maskUnits="userSpaceOnUse" x="994.631" y="4443.414" width="1431.02" height="335.707" id="SVGID_00000178911166895505624210000004909463419227699897_">
											<g style="filter:url(#Adobe_OpacityMaskFilter_00000107581894359260270560000014028769201051540654_);">
											</g>
										</mask>
										
											<linearGradient id="SVGID_00000018923034658780437480000016709853459312162205_" gradientUnits="userSpaceOnUse" x1="994.6314" y1="4611.2676" x2="2425.6511" y2="4611.2676">
											<stop offset="0" style="stop-color:#FFFFFF"/>
											<stop offset="0.0074" style="stop-color:#EEEEEE"/>
											<stop offset="0.0273" style="stop-color:#C4C4C4"/>
											<stop offset="0.0477" style="stop-color:#A2A2A2"/>
											<stop offset="0.0683" style="stop-color:#888888"/>
											<stop offset="0.0894" style="stop-color:#757575"/>
											<stop offset="0.1111" style="stop-color:#6A6A6A"/>
											<stop offset="0.1344" style="stop-color:#666666"/>
											<stop offset="0.3495" style="stop-color:#666666"/>
											<stop offset="0.4821" style="stop-color:#5D5D5D"/>
											<stop offset="0.7004" style="stop-color:#444444"/>
											<stop offset="0.8226" style="stop-color:#333333"/>
											<stop offset="1" style="stop-color:#FFFFFF"/>
										</linearGradient>
										
											<path style="mask:url(#SVGID_00000178911166895505624210000004909463419227699897_);fill:url(#SVGID_00000018923034658780437480000016709853459312162205_);" d="            M2424.274,4620.593c-0.543,3.497-1.183,6.958-2.022,10.455c-0.099,0.692-0.295,1.42-0.542,2.113            c-3.303,13.186-8.728,26.044-16.272,38.394c-3.403,5.5-7.15,10.892-11.341,16.137l-1.134,1.347            c-22.336,27.976-54.977,50.524-94.176,66.042c-29.584,11.766-62.914,19.525-98.367,22.548            c-11.29,0.983-22.779,1.494-34.465,1.494h-911.481c-10.602,0-21.104-0.401-31.409-1.238            c-83.723-6.558-156.005-39.159-195.796-88.917l-1.133-1.384c-13.559-16.974-22.829-35.37-27.613-54.421            c-0.245-0.729-0.393-1.421-0.492-2.113c-0.839-3.497-1.529-6.958-2.022-10.455c-1.479-9.653-1.726-19.488-0.938-29.324            c0.247-3.132,0.642-6.192,1.134-9.325l0.197-1.166l0.297-2.076l13.806-87.861l7.445-47.354            c-1.973,12.495-1.924,24.989,0.148,37.191c0.738,4.517,1.725,9.034,3.056,13.515c0.346,1.275,0.741,2.586,1.183,3.897            v0.037c3.649,11.147,8.975,21.965,15.976,32.347c0.74,1.129,1.528,2.258,2.367,3.388c0.198,0.328,0.444,0.655,0.74,0.947            v0.036c0.739,1.166,1.627,2.259,2.564,3.387v0.037c1.085,1.494,2.268,2.95,3.501,4.444l1.085,1.312            c3.253,3.934,6.803,7.723,10.501,11.401c0.148,0.183,0.297,0.328,0.444,0.474c45.609,45.387,123.02,72.707,209.405,72.707            h882.539c27.218,0,53.498-2.732,78.201-7.868c60.351-12.495,111.135-39.486,142.003-76.642l1.083-1.312            c1.135-1.348,2.22-2.695,3.255-4.043v-0.037c0.838-1.129,1.727-2.222,2.564-3.351c0.198-0.292,0.395-0.583,0.641-0.911            c0.789-1.129,1.578-2.259,2.366-3.388c5.128-7.541,9.369-15.299,12.672-23.277c0.741-1.53,1.332-3.132,1.922-4.662            c0,0,0,0,0-0.037c0.543-1.494,1.135-3.024,1.578-4.517v-0.037c0.542-1.603,1.084-3.206,1.479-4.808            c4.684-16.392,5.769-33.294,3.058-50.305l3.155,20.035l7.593,48.229l10.551,67.025l0.297,2.076l0.198,1.056            c0.493,3.169,0.887,6.265,1.183,9.434C2425.999,4601.104,2425.704,4610.94,2424.274,4620.593z"/>
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000019668484823061603800000017194313027822076602_" gradientUnits="userSpaceOnUse" x1="994.6314" y1="4611.2676" x2="2425.6511" y2="4611.2676">
									<stop offset="0" style="stop-color:#FDFDFD"/>
									<stop offset="0.0027" style="stop-color:#F2F2F2"/>
									<stop offset="0.018" style="stop-color:#BABABA"/>
									<stop offset="0.0338" style="stop-color:#898989"/>
									<stop offset="0.0498" style="stop-color:#5F5F5F"/>
									<stop offset="0.0659" style="stop-color:#3C3C3C"/>
									<stop offset="0.0822" style="stop-color:#222222"/>
									<stop offset="0.0989" style="stop-color:#0F0F0F"/>
									<stop offset="0.116" style="stop-color:#040404"/>
									<stop offset="0.1344" style="stop-color:#000000"/>
									<stop offset="0.1553" style="stop-color:#090909"/>
									<stop offset="0.1899" style="stop-color:#222222"/>
									<stop offset="0.2339" style="stop-color:#4A4B4B"/>
									<stop offset="0.2855" style="stop-color:#828384"/>
									<stop offset="0.3429" style="stop-color:#C9CACB"/>
									<stop offset="0.3495" style="stop-color:#D1D3D4"/>
									<stop offset="0.4084" style="stop-color:#C8CACB"/>
									<stop offset="0.5058" style="stop-color:#B0B1B2"/>
									<stop offset="0.6294" style="stop-color:#888889"/>
									<stop offset="0.7733" style="stop-color:#525051"/>
									<stop offset="0.8226" style="stop-color:#3E3B3C"/>
									<stop offset="1" style="stop-color:#FFFFFF"/>
								</linearGradient>
								<path style="opacity:0.4;fill:url(#SVGID_00000019668484823061603800000017194313027822076602_);" d="M2424.274,4620.593          c-0.543,3.497-1.183,6.958-2.022,10.455c-0.099,0.692-0.295,1.42-0.542,2.113c-3.303,13.186-8.728,26.044-16.272,38.394          c-3.403,5.5-7.15,10.892-11.341,16.137l-1.134,1.347c-22.336,27.976-54.977,50.524-94.176,66.042          c-29.584,11.766-62.914,19.525-98.367,22.548c-11.29,0.983-22.779,1.494-34.465,1.494h-911.481          c-10.602,0-21.104-0.401-31.409-1.238c-83.723-6.558-156.005-39.159-195.796-88.917l-1.133-1.384          c-13.559-16.974-22.829-35.37-27.613-54.421c-0.245-0.729-0.393-1.421-0.492-2.113c-0.839-3.497-1.529-6.958-2.022-10.455          c-1.479-9.653-1.726-19.488-0.938-29.324c0.247-3.132,0.642-6.192,1.134-9.325l0.197-1.166l0.297-2.076l13.806-87.861          l7.445-47.354c-1.973,12.495-1.924,24.989,0.148,37.191c0.738,4.517,1.725,9.034,3.056,13.515          c0.346,1.275,0.741,2.586,1.183,3.897v0.037c3.649,11.147,8.975,21.965,15.976,32.347c0.74,1.129,1.528,2.258,2.367,3.388          c0.198,0.328,0.444,0.655,0.74,0.947v0.036c0.739,1.166,1.627,2.259,2.564,3.387v0.037c1.085,1.494,2.268,2.95,3.501,4.444          l1.085,1.312c3.253,3.934,6.803,7.723,10.501,11.401c0.148,0.183,0.297,0.328,0.444,0.474          c45.609,45.387,123.02,72.707,209.405,72.707h882.539c27.218,0,53.498-2.732,78.201-7.868          c60.351-12.495,111.135-39.486,142.003-76.642l1.083-1.312c1.135-1.348,2.22-2.695,3.255-4.043v-0.037          c0.838-1.129,1.727-2.222,2.564-3.351c0.198-0.292,0.395-0.583,0.641-0.911c0.789-1.129,1.578-2.259,2.366-3.388          c5.128-7.541,9.369-15.299,12.672-23.277c0.741-1.53,1.332-3.132,1.922-4.662c0,0,0,0,0-0.037          c0.543-1.494,1.135-3.024,1.578-4.517v-0.037c0.542-1.603,1.084-3.206,1.479-4.808c4.684-16.392,5.769-33.294,3.058-50.305          l3.155,20.035l7.593,48.229l10.551,67.025l0.297,2.076l0.198,1.056c0.493,3.169,0.887,6.265,1.183,9.434          C2425.999,4601.104,2425.704,4610.94,2424.274,4620.593z"/>
							</g>
						</g>
					</g>
					<g>
						<rect x="1041.863" y="1847.37" style="fill:#E6E6E6;" width="1334.693" height="2762.465"/>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000035506845053333332310000004998927104447448707_" filterUnits="userSpaceOnUse" x="1041.863" y="1847.37" width="1334.693" height="2762.465">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1041.863" y="1847.37" width="1334.693" height="2762.465" id="SVGID_00000096747389745243833060000007885073268447463612_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000035506845053333332310000004998927104447448707_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000138574129581718338360000018053756365259631009_" filterUnits="userSpaceOnUse" x="1041.863" y="1847.37" width="1334.693" height="2762.465">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1041.863" y="1847.37" width="1334.693" height="2762.465" id="SVGID_00000096747389745243833060000007885073268447463612_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000138574129581718338360000018053756365259631009_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000041284804014050056340000011693193222731834275_" gradientUnits="userSpaceOnUse" x1="1041.8625" y1="3228.6023" x2="2376.5552" y2="3228.6023">
									<stop offset="0" style="stop-color:#FFFFFF"/>
									<stop offset="0.0074" style="stop-color:#EEEEEE"/>
									<stop offset="0.0273" style="stop-color:#C4C4C4"/>
									<stop offset="0.0477" style="stop-color:#A2A2A2"/>
									<stop offset="0.0683" style="stop-color:#888888"/>
									<stop offset="0.0894" style="stop-color:#757575"/>
									<stop offset="0.1111" style="stop-color:#6A6A6A"/>
									<stop offset="0.1344" style="stop-color:#666666"/>
									<stop offset="0.3495" style="stop-color:#666666"/>
									<stop offset="0.4821" style="stop-color:#5D5D5D"/>
									<stop offset="0.7004" style="stop-color:#444444"/>
									<stop offset="0.8226" style="stop-color:#333333"/>
									<stop offset="1" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<rect x="1041.863" y="1847.37" style="mask:url(#SVGID_00000096747389745243833060000007885073268447463612_);fill:url(#SVGID_00000041284804014050056340000011693193222731834275_);" width="1334.693" height="2762.465"/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000049216122254118687150000013528249317403876513_" gradientUnits="userSpaceOnUse" x1="1041.8625" y1="3228.6023" x2="2376.5552" y2="3228.6023">
							<stop offset="0" style="stop-color:#FDFDFD"/>
							<stop offset="0.0027" style="stop-color:#F2F2F2"/>
							<stop offset="0.018" style="stop-color:#BABABA"/>
							<stop offset="0.0338" style="stop-color:#898989"/>
							<stop offset="0.0498" style="stop-color:#5F5F5F"/>
							<stop offset="0.0659" style="stop-color:#3C3C3C"/>
							<stop offset="0.0822" style="stop-color:#222222"/>
							<stop offset="0.0989" style="stop-color:#0F0F0F"/>
							<stop offset="0.116" style="stop-color:#040404"/>
							<stop offset="0.1344" style="stop-color:#000000"/>
							<stop offset="0.1553" style="stop-color:#090909"/>
							<stop offset="0.1899" style="stop-color:#222222"/>
							<stop offset="0.2339" style="stop-color:#4A4B4B"/>
							<stop offset="0.2855" style="stop-color:#828384"/>
							<stop offset="0.3429" style="stop-color:#C9CACB"/>
							<stop offset="0.3495" style="stop-color:#D1D3D4"/>
							<stop offset="0.4084" style="stop-color:#C8CACB"/>
							<stop offset="0.5058" style="stop-color:#B0B1B2"/>
							<stop offset="0.6294" style="stop-color:#888889"/>
							<stop offset="0.7733" style="stop-color:#525051"/>
							<stop offset="0.8226" style="stop-color:#3E3B3C"/>
							<stop offset="1" style="stop-color:#FFFFFF"/>
						</linearGradient>
						
							<rect x="1041.863" y="1847.37" style="opacity:0.4;fill:url(#SVGID_00000049216122254118687150000013528249317403876513_);" width="1334.693" height="2762.465"/>
						<g>
							<g>
								<g>
									<g>
										<path style="fill:#000301;" d="M1112.974,1820.129v62.202h-0.372c-6.902,0-13.359,2.004-18.705,5.568            c-9.499,6.161-15.736,16.775-15.736,28.876v0.37h-62.129v-97.015H1112.974z"/>
										<path style="fill:#8A8A8A;" d="M1023.404,1909.661v-82.115h82.115v47.962c-17.531,3.066-31.228,16.806-34.196,34.153            H1023.404z"/>
									</g>
									<g>
										
											<ellipse transform="matrix(0.9951 -0.0985 0.0985 0.9951 -180.1564 112.7708)" style="fill:#FFFFFF;" cx="1051.587" cy="1880.247" rx="9.742" ry="9.743"/>
										<path style="fill:#FFFFFF;" d="M1087.077,1854.499c0,5.381-4.362,9.743-9.742,9.743c-5.381,0-9.743-4.362-9.743-9.743            c0-5.38,4.362-9.742,9.743-9.742C1082.715,1844.757,1087.077,1849.119,1087.077,1854.499z"/>
									</g>
									<g style="opacity:0.54;">
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000116195396211969897160000004491953182161871518_" filterUnits="userSpaceOnUse" x="1041.845" y="1870.505" width="19.485" height="19.485">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1041.845" y="1870.505" width="19.485" height="19.485" id="SVGID_00000083804728258150177070000003727900489458094730_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000116195396211969897160000004491953182161871518_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000111181291448599973220000011418067644172934016_" filterUnits="userSpaceOnUse" x="1041.845" y="1870.505" width="19.485" height="19.485">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1041.845" y="1870.505" width="19.485" height="19.485" id="SVGID_00000083804728258150177070000003727900489458094730_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000111181291448599973220000011418067644172934016_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000141438572352646235160000004786917922025468800_" gradientUnits="userSpaceOnUse" x1="1041.8448" y1="1880.2473" x2="1061.3298" y2="1880.2473">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<circle style="mask:url(#SVGID_00000083804728258150177070000003727900489458094730_);fill:url(#SVGID_00000141438572352646235160000004786917922025468800_);" cx="1051.587" cy="1880.247" r="9.743"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000181051493787890519950000003952308604864035765_" gradientUnits="userSpaceOnUse" x1="1041.8448" y1="1880.2473" x2="1061.3298" y2="1880.2473">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<circle style="fill:url(#SVGID_00000181051493787890519950000003952308604864035765_);" cx="1051.587" cy="1880.247" r="9.743"/>
										</g>
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000098194376132337511680000007341493694566571904_" filterUnits="userSpaceOnUse" x="1067.593" y="1844.757" width="19.484" height="19.484">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1067.593" y="1844.757" width="19.484" height="19.484" id="SVGID_00000000917091674693459730000016735413129537836948_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000098194376132337511680000007341493694566571904_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000050663905840678500950000008621311144216018589_" filterUnits="userSpaceOnUse" x="1067.593" y="1844.757" width="19.484" height="19.484">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1067.593" y="1844.757" width="19.484" height="19.484" id="SVGID_00000000917091674693459730000016735413129537836948_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000050663905840678500950000008621311144216018589_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000072269800828215478350000000482100349133626262_" gradientUnits="userSpaceOnUse" x1="1067.5928" y1="1854.4998" x2="1087.0771" y2="1854.4998">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000000917091674693459730000016735413129537836948_);fill:url(#SVGID_00000072269800828215478350000000482100349133626262_);" d="               M1087.077,1854.499c0,5.381-4.362,9.743-9.742,9.743c-5.381,0-9.743-4.362-9.743-9.743c0-5.38,4.362-9.742,9.743-9.742               C1082.715,1844.757,1087.077,1849.119,1087.077,1854.499z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000121242984028155774200000004986831882800986541_" gradientUnits="userSpaceOnUse" x1="1067.5928" y1="1854.4998" x2="1087.0771" y2="1854.4998">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000121242984028155774200000004986831882800986541_);" d="M1087.077,1854.499             c0,5.381-4.362,9.743-9.742,9.743c-5.381,0-9.743-4.362-9.743-9.743c0-5.38,4.362-9.742,9.743-9.742             C1082.715,1844.757,1087.077,1849.119,1087.077,1854.499z"/>
										</g>
									</g>
								</g>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M1112.974,1820.129v50.327c-2.375,0.519-4.898,0.964-7.423,1.41          c-8.981,1.484-18.482,2.45-28.207,2.672c-2.301,0.149-4.676,0.149-7.051,0.149c-3.711,0-7.274-0.074-10.837-0.223h-0.074          c-4.973-0.296-9.799-0.667-14.474-1.188h-0.148c-0.966-0.148-1.93-0.222-2.897-0.445c-6.382-0.742-12.47-1.855-18.408-3.192          h-0.074c-2.523-0.593-4.899-1.186-7.349-1.855v-47.654H1112.974z"/>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M1112.974,1820.129v62.202h-0.372c-6.902,0-13.359,2.004-18.705,5.568          c-2.005-1.559-4.009-3.192-6.013-4.824h-0.073c-3.563-2.821-6.979-5.567-10.468-8.537c-0.964-0.816-2.005-1.633-2.968-2.449          c-5.79-4.824-11.357-9.575-16.775-14.4c-3.861-3.415-7.572-6.904-11.208-10.318c-3.563-3.266-6.977-6.457-10.319-9.724          c-3.414-3.341-6.828-6.755-10.094-10.095c-2.45-2.524-4.9-4.974-7.2-7.423H1112.974z"/>
							</g>
							<g>
								<g>
									<g>
										<path style="fill:#000301;" d="M1112.974,4591.797v62.128h-97.016v-96.941h62.203v0.371            c0,6.903,2.005,13.361,5.568,18.706c6.159,9.501,16.775,15.737,28.873,15.737H1112.974z"/>
										<path style="fill:#8A8A8A;" d="M1105.539,4646.536h-82.114v-82.115h47.961c3.067,17.532,16.807,31.229,34.153,34.197            V4646.536z"/>
									</g>
									<g>
										<path style="fill:#FFFFFF;" d="M1076.126,4608.61c5.38,0,9.742,4.363,9.742,9.743c0,5.381-4.362,9.743-9.742,9.743            c-5.381,0-9.743-4.362-9.743-9.743C1066.384,4612.973,1070.745,4608.61,1076.126,4608.61z"/>
										<path style="fill:#FFFFFF;" d="M1050.378,4582.863c5.381,0,9.743,4.361,9.743,9.742c0,5.381-4.362,9.743-9.743,9.743            c-5.38,0-9.742-4.362-9.742-9.743C1040.636,4587.224,1044.998,4582.863,1050.378,4582.863z"/>
									</g>
									<g style="opacity:0.54;">
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000080207527495908568670000008644986091075806102_" filterUnits="userSpaceOnUse" x="1066.384" y="4608.61" width="19.484" height="19.485">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1066.384" y="4608.61" width="19.484" height="19.485" id="SVGID_00000024002113307473354490000014460249167679066303_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000080207527495908568670000008644986091075806102_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000046310151088176054690000011634137260131555258_" filterUnits="userSpaceOnUse" x="1066.384" y="4608.61" width="19.484" height="19.485">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1066.384" y="4608.61" width="19.484" height="19.485" id="SVGID_00000024002113307473354490000014460249167679066303_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000046310151088176054690000011634137260131555258_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000111893306369843522920000001694517522507562148_" gradientUnits="userSpaceOnUse" x1="-5732.3359" y1="3577.4299" x2="-5712.8511" y2="3577.4299" gradientTransform="matrix(0 -1 1 0 -2501.3042 -1104.2408)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000024002113307473354490000014460249167679066303_);fill:url(#SVGID_00000111893306369843522920000001694517522507562148_);" d="               M1076.126,4608.61c5.38,0,9.742,4.363,9.742,9.743c0,5.381-4.362,9.743-9.742,9.743c-5.381,0-9.743-4.362-9.743-9.743               C1066.384,4612.973,1070.745,4608.61,1076.126,4608.61z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000039133600751538488020000002046853819076755601_" gradientUnits="userSpaceOnUse" x1="-5732.3359" y1="3577.4299" x2="-5712.8511" y2="3577.4299" gradientTransform="matrix(0 -1 1 0 -2501.3042 -1104.2408)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000039133600751538488020000002046853819076755601_);" d="M1076.126,4608.61             c5.38,0,9.742,4.363,9.742,9.743c0,5.381-4.362,9.743-9.742,9.743c-5.381,0-9.743-4.362-9.743-9.743             C1066.384,4612.973,1070.745,4608.61,1076.126,4608.61z"/>
										</g>
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000006706158996232335310000007281474898569992106_" filterUnits="userSpaceOnUse" x="1040.636" y="4582.863" width="19.485" height="19.484">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="1040.636" y="4582.863" width="19.485" height="19.484" id="SVGID_00000089565107278704696590000011807573336347088517_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000006706158996232335310000007281474898569992106_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000080199153613495622720000009487700127987000482_" filterUnits="userSpaceOnUse" x="1040.636" y="4582.863" width="19.485" height="19.484">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="1040.636" y="4582.863" width="19.485" height="19.484" id="SVGID_00000089565107278704696590000011807573336347088517_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000080199153613495622720000009487700127987000482_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000069400033969620436030000005659077192861441936_" gradientUnits="userSpaceOnUse" x1="-5706.5879" y1="3551.6824" x2="-5687.1035" y2="3551.6824" gradientTransform="matrix(0 -1 1 0 -2501.3042 -1104.2408)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000089565107278704696590000011807573336347088517_);fill:url(#SVGID_00000069400033969620436030000005659077192861441936_);" d="               M1050.378,4582.863c5.381,0,9.743,4.361,9.743,9.742c0,5.381-4.362,9.743-9.743,9.743c-5.38,0-9.742-4.362-9.742-9.743               C1040.636,4587.224,1044.998,4582.863,1050.378,4582.863z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000065038808680071727680000017880595904398186899_" gradientUnits="userSpaceOnUse" x1="-5706.5879" y1="3551.6824" x2="-5687.1035" y2="3551.6824" gradientTransform="matrix(0 -1 1 0 -2501.3042 -1104.2408)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000065038808680071727680000017880595904398186899_);" d="M1050.378,4582.863             c5.381,0,9.743,4.361,9.743,9.742c0,5.381-4.362,9.743-9.743,9.743c-5.38,0-9.742-4.362-9.742-9.743             C1040.636,4587.224,1044.998,4582.863,1050.378,4582.863z"/>
										</g>
									</g>
								</g>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M1070.516,4599.665c0,3.488-0.074,6.829-0.223,10.17v0.741          c-0.297,4.974-0.668,9.799-1.186,14.475v0.074c-0.892,7.422-2.079,14.549-3.64,21.377c-0.593,2.524-1.186,4.973-1.855,7.423          h-47.654v-96.941h50.327c0.52,2.375,0.966,4.898,1.411,7.423c1.482,8.981,2.449,18.482,2.671,28.206          C1070.516,4594.915,1070.516,4597.29,1070.516,4599.665z"/>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M1083.729,4576.061c-1.56,2.004-3.192,4.009-4.826,6.013          c-2.745,3.562-5.567,7.051-8.536,10.539c-0.815,0.966-1.632,2.005-2.45,2.97c-4.081,4.899-8.089,9.649-12.172,14.252          c-0.743,0.89-1.484,1.707-2.227,2.523c-6.754,7.497-13.436,14.697-20.042,21.526c-3.34,3.414-6.681,6.755-10.021,10.021          l-0.074,0.074c-2.523,2.45-4.973,4.899-7.423,7.2v-94.195h62.203v0.371          C1078.161,4564.258,1080.166,4570.716,1083.729,4576.061z"/>
							</g>
							<g>
								<g>
									<g>
										<path style="fill:#000301;" d="M2406.841,4556.91V4654h-96.942v-62.203h0.371c6.829,0,13.36-2.004,18.706-5.567            c9.499-6.161,15.661-16.776,15.661-28.875v-0.445H2406.841z"/>
										<path style="fill:#8A8A8A;" d="M2399.413,4564.401v82.114h-82.115v-47.961c17.531-3.066,31.228-16.807,34.196-34.153            H2399.413z"/>
									</g>
									<g>
										<path style="fill:#FFFFFF;" d="M2361.487,4593.813c0-5.38,4.363-9.742,9.742-9.742c5.381,0,9.743,4.362,9.743,9.742            c0,5.381-4.362,9.743-9.743,9.743C2365.85,4603.556,2361.487,4599.194,2361.487,4593.813z"/>
										<path style="fill:#FFFFFF;" d="M2335.74,4619.562c0-5.38,4.361-9.743,9.741-9.743c5.381,0,9.743,4.363,9.743,9.743            c0,5.381-4.362,9.743-9.743,9.743C2340.101,4629.304,2335.74,4624.943,2335.74,4619.562z"/>
									</g>
									<g style="opacity:0.54;">
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000076569571644347635480000012233877964038983586_" filterUnits="userSpaceOnUse" x="2361.487" y="4584.072" width="19.485" height="19.484">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2361.487" y="4584.072" width="19.485" height="19.484" id="SVGID_00000006698563631954840540000000868389598763996292_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000076569571644347635480000012233877964038983586_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000130628829055317951210000013526981251186693055_" filterUnits="userSpaceOnUse" x="2361.487" y="4584.072" width="19.485" height="19.484">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2361.487" y="4584.072" width="19.485" height="19.484" id="SVGID_00000006698563631954840540000000868389598763996292_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000130628829055317951210000013526981251186693055_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000000210539682463705500000016995944226902062226_" gradientUnits="userSpaceOnUse" x1="-5791.208" y1="-3043.4448" x2="-5771.7227" y2="-3043.4448" gradientTransform="matrix(-1 0 0 -1 -3410.2358 1550.3691)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000006698563631954840540000000868389598763996292_);fill:url(#SVGID_00000000210539682463705500000016995944226902062226_);" d="               M2361.487,4593.813c0-5.38,4.363-9.742,9.742-9.742c5.381,0,9.743,4.362,9.743,9.742c0,5.381-4.362,9.743-9.743,9.743               C2365.85,4603.556,2361.487,4599.194,2361.487,4593.813z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000085252637321488479960000011124971982091844274_" gradientUnits="userSpaceOnUse" x1="-5791.208" y1="-3043.4448" x2="-5771.7227" y2="-3043.4448" gradientTransform="matrix(-1 0 0 -1 -3410.2358 1550.3691)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000085252637321488479960000011124971982091844274_);" d="M2361.487,4593.813             c0-5.38,4.363-9.742,9.742-9.742c5.381,0,9.743,4.362,9.743,9.742c0,5.381-4.362,9.743-9.743,9.743             C2365.85,4603.556,2361.487,4599.194,2361.487,4593.813z"/>
										</g>
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000029758279603261970360000017465063913971356298_" filterUnits="userSpaceOnUse" x="2335.74" y="4609.819" width="19.484" height="19.485">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2335.74" y="4609.819" width="19.484" height="19.485" id="SVGID_00000066483127296432951190000008477556474990791325_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000029758279603261970360000017465063913971356298_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000114057374190398714610000005611035596507984564_" filterUnits="userSpaceOnUse" x="2335.74" y="4609.819" width="19.484" height="19.485">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2335.74" y="4609.819" width="19.484" height="19.485" id="SVGID_00000066483127296432951190000008477556474990791325_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000114057374190398714610000005611035596507984564_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000009577908524117927190000006882820095602356357_" gradientUnits="userSpaceOnUse" x1="-5765.46" y1="-3069.1924" x2="-5745.9756" y2="-3069.1924" gradientTransform="matrix(-1 0 0 -1 -3410.2358 1550.3691)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000066483127296432951190000008477556474990791325_);fill:url(#SVGID_00000009577908524117927190000006882820095602356357_);" d="               M2335.74,4619.562c0-5.38,4.361-9.743,9.741-9.743c5.381,0,9.743,4.363,9.743,9.743c0,5.381-4.362,9.743-9.743,9.743               C2340.101,4629.304,2335.74,4624.943,2335.74,4619.562z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000069381233050875958160000015871118473126046101_" gradientUnits="userSpaceOnUse" x1="-5765.46" y1="-3069.1924" x2="-5745.9756" y2="-3069.1924" gradientTransform="matrix(-1 0 0 -1 -3410.2358 1550.3691)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000069381233050875958160000015871118473126046101_);" d="M2335.74,4619.562             c0-5.38,4.361-9.743,9.741-9.743c5.381,0,9.743,4.363,9.743,9.743c0,5.381-4.362,9.743-9.743,9.743             C2340.101,4629.304,2335.74,4624.943,2335.74,4619.562z"/>
										</g>
									</g>
								</g>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M2406.841,4606.346V4654h-96.942v-50.327c2.376-0.593,4.898-0.965,7.423-1.411          c8.907-1.484,18.409-2.449,28.208-2.746c2.301-0.148,4.676-0.148,7.052-0.148c3.636,0,7.199,0.073,10.762,0.222          c0.074,0,0.074,0,0.148,0c4.529,0.371,8.906,0.668,13.064,1.114c0.446,0.074,0.889,0.074,1.335,0.147h0.148          c7.423,0.891,14.548,2.078,21.379,3.638C2401.942,4605.009,2404.316,4605.678,2406.841,4606.346z"/>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M2404.019,4654h-94.121v-62.203h0.371c6.829,0,13.36-2.004,18.706-5.567          c2.004,1.559,4.008,3.191,6.012,4.825c3.563,2.672,7.051,5.567,10.54,8.462c0.964,0.891,2.004,1.633,2.969,2.523          c3.116,2.598,6.161,5.122,9.13,7.794c2.524,2.152,5.049,4.379,7.571,6.606c7.571,6.753,14.697,13.435,21.601,19.966          c3.415,3.415,6.755,6.755,10.02,10.095l0.075,0.074C2399.344,4649.101,2401.794,4651.476,2404.019,4654z"/>
							</g>
							<g>
								<g>
									<g>
										<path style="fill:#000301;" d="M2406.841,1820.129v96.941h-62.204v-0.371c0-6.903-2.004-13.361-5.567-18.705            c-6.161-9.501-16.776-15.737-28.874-15.737h-0.371v-62.128H2406.841z"/>
										<path style="fill:#8A8A8A;" d="M2317.278,1827.525h82.114v82.115h-47.961c-3.067-17.531-16.806-31.228-34.153-34.197            V1827.525z"/>
									</g>
									<g>
										<path style="fill:#FFFFFF;" d="M2346.691,1865.451c-5.38,0-9.742-4.361-9.742-9.743c0-5.38,4.362-9.742,9.742-9.742            c5.381,0,9.742,4.362,9.742,9.742C2356.433,1861.089,2352.072,1865.451,2346.691,1865.451z"/>
										
											<ellipse transform="matrix(0.2298 -0.9732 0.9732 0.2298 -3.7634 3758.1575)" style="fill:#FFFFFF;" cx="2372.439" cy="1881.456" rx="9.743" ry="9.743"/>
									</g>
									<g style="opacity:0.54;">
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000168826418633055163100000006029575043764765096_" filterUnits="userSpaceOnUse" x="2336.949" y="1845.966" width="19.484" height="19.484">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2336.949" y="1845.966" width="19.484" height="19.484" id="SVGID_00000013888833868810174500000015613074688565458820_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000168826418633055163100000006029575043764765096_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000067949566454138492840000014573427233811307965_" filterUnits="userSpaceOnUse" x="2336.949" y="1845.966" width="19.484" height="19.484">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2336.949" y="1845.966" width="19.484" height="19.484" id="SVGID_00000013888833868810174500000015613074688565458820_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000067949566454138492840000014573427233811307965_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000163073701578471788980000011599093815273552277_" gradientUnits="userSpaceOnUse" x1="-70.6066" y1="-3645.6221" x2="-51.1222" y2="-3645.6221" gradientTransform="matrix(0 1 -1 0 -1298.9312 1916.573)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<path style="mask:url(#SVGID_00000013888833868810174500000015613074688565458820_);fill:url(#SVGID_00000163073701578471788980000011599093815273552277_);" d="               M2346.691,1865.451c-5.38,0-9.742-4.361-9.742-9.743c0-5.38,4.362-9.742,9.742-9.742c5.381,0,9.742,4.362,9.742,9.742               C2356.433,1861.089,2352.072,1865.451,2346.691,1865.451z"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000182526235610227503770000007918425020773754553_" gradientUnits="userSpaceOnUse" x1="-70.6066" y1="-3645.6221" x2="-51.1222" y2="-3645.6221" gradientTransform="matrix(0 1 -1 0 -1298.9312 1916.573)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											<path style="fill:url(#SVGID_00000182526235610227503770000007918425020773754553_);" d="M2346.691,1865.451             c-5.38,0-9.742-4.361-9.742-9.743c0-5.38,4.362-9.742,9.742-9.742c5.381,0,9.742,4.362,9.742,9.742             C2356.433,1861.089,2352.072,1865.451,2346.691,1865.451z"/>
										</g>
										<g>
											<defs>
												
													<filter id="Adobe_OpacityMaskFilter_00000133528588464677080540000005200223142484832409_" filterUnits="userSpaceOnUse" x="2362.696" y="1871.714" width="19.485" height="19.485">
													<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
													<feBlend in="SourceGraphic" in2="back" mode="normal"/>
												</filter>
											</defs>
											
												<mask maskUnits="userSpaceOnUse" x="2362.696" y="1871.714" width="19.485" height="19.485" id="SVGID_00000145776417689612393730000016624780863431999888_">
												<g style="filter:url(#Adobe_OpacityMaskFilter_00000133528588464677080540000005200223142484832409_);">
													<defs>
														
															<filter id="Adobe_OpacityMaskFilter_00000122710214946128973210000010400767878546018468_" filterUnits="userSpaceOnUse" x="2362.696" y="1871.714" width="19.485" height="19.485">
															<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
															<feBlend in="SourceGraphic" in2="back" mode="normal"/>
														</filter>
													</defs>
													
														<mask maskUnits="userSpaceOnUse" x="2362.696" y="1871.714" width="19.485" height="19.485" id="SVGID_00000145776417689612393730000016624780863431999888_">
														<g style="filter:url(#Adobe_OpacityMaskFilter_00000122710214946128973210000010400767878546018468_);">
														</g>
													</mask>
													
														<linearGradient id="SVGID_00000129923562976340218340000005997890180507520414_" gradientUnits="userSpaceOnUse" x1="-44.8592" y1="-3671.3699" x2="-25.3741" y2="-3671.3699" gradientTransform="matrix(0 1 -1 0 -1298.9312 1916.573)">
														<stop offset="0" style="stop-color:#FFFFFF"/>
														<stop offset="0.0575" style="stop-color:#F0F0F0"/>
														<stop offset="0.1676" style="stop-color:#CACACA"/>
														<stop offset="0.3174" style="stop-color:#8C8C8C"/>
														<stop offset="0.457" style="stop-color:#4D4D4D"/>
														<stop offset="0.517" style="stop-color:#565656"/>
														<stop offset="0.6162" style="stop-color:#6F6F6F"/>
														<stop offset="0.7423" style="stop-color:#989898"/>
														<stop offset="0.8887" style="stop-color:#D0D0D0"/>
														<stop offset="1" style="stop-color:#FFFFFF"/>
													</linearGradient>
													
														<circle style="mask:url(#SVGID_00000145776417689612393730000016624780863431999888_);fill:url(#SVGID_00000129923562976340218340000005997890180507520414_);" cx="2372.439" cy="1881.456" r="9.743"/>
												</g>
											</mask>
											
												<linearGradient id="SVGID_00000054256777643271977850000009503473228081973164_" gradientUnits="userSpaceOnUse" x1="-44.8592" y1="-3671.3699" x2="-25.3741" y2="-3671.3699" gradientTransform="matrix(0 1 -1 0 -1298.9312 1916.573)">
												<stop offset="0" style="stop-color:#FDFDFD"/>
												<stop offset="0.0475" style="stop-color:#EEF4F9"/>
												<stop offset="0.1384" style="stop-color:#C8DDEE"/>
												<stop offset="0.2634" style="stop-color:#89B7DC"/>
												<stop offset="0.4163" style="stop-color:#3483C3"/>
												<stop offset="0.457" style="stop-color:#1C75BC"/>
												<stop offset="0.5072" style="stop-color:#257BBF"/>
												<stop offset="0.5902" style="stop-color:#3E8AC6"/>
												<stop offset="0.6957" style="stop-color:#67A3D2"/>
												<stop offset="0.8195" style="stop-color:#A0C5E3"/>
												<stop offset="0.9573" style="stop-color:#E7F1F8"/>
												<stop offset="1" style="stop-color:#FFFFFF"/>
											</linearGradient>
											
												<circle style="fill:url(#SVGID_00000054256777643271977850000009503473228081973164_);" cx="2372.439" cy="1881.456" r="9.743"/>
										</g>
									</g>
								</g>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M2406.841,1820.129v96.941h-50.327c-0.52-2.375-0.964-4.899-1.41-7.423          c-1.486-8.981-2.45-18.482-2.673-28.206c-0.148-2.302-0.148-4.677-0.148-7.052c0-3.711,0.074-7.349,0.223-10.911          c0.297-4.974,0.669-9.798,1.189-14.475v-0.075c0.073-0.519,0.147-1.038,0.222-1.558c0.817-6.903,1.929-13.51,3.414-19.818          c0.594-2.524,1.189-4.974,1.855-7.423H2406.841z"/>
								<path style="opacity:0.23;fill:#FFFFFF;" d="M2406.841,1822.874v94.196h-62.204v-0.371c0-6.903-2.004-13.361-5.567-18.705          c1.559-2.004,3.193-4.009,4.826-6.013c2.745-3.563,5.567-7.051,8.535-10.54c0.817-0.965,1.633-2.004,2.45-2.969          c4.824-5.79,9.574-11.357,14.4-16.776c2.45-2.672,4.826-5.345,7.274-7.942c4.231-4.677,8.537-9.204,12.766-13.584          c3.342-3.414,6.756-6.828,10.096-10.095C2401.942,1827.626,2404.39,1825.176,2406.841,1822.874z"/>
							</g>
							<g>
								<rect x="1016.032" y="1917.144" style="fill:#000301;" width="62.129" height="2639.841"/>
								<rect x="1023.434" y="1924.537" style="fill:#8A8A8A;" width="47.284" height="2625.025"/>
							</g>
							<g>
								<rect x="2345.454" y="1917.144" style="fill:#000301;" width="62.128" height="2639.841"/>
								<rect x="2352.903" y="1924.537" style="fill:#8A8A8A;" width="47.284" height="2625.025"/>
							</g>
							<g>
								<rect x="1112.974" y="1820.129" style="fill:#000301;" width="1196.85" height="62.202"/>
								<rect x="1120.38" y="1827.523" style="fill:#8A8A8A;" width="1182.021" height="47.356"/>
							</g>
							<g>
								<rect x="1112.974" y="4591.723" style="fill:#000301;" width="1196.85" height="62.203"/>
								<rect x="1120.38" y="4599.147" style="fill:#8A8A8A;" width="1182.021" height="47.356"/>
							</g>
						</g>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000003813385926652374230000005625262885488300419_" filterUnits="userSpaceOnUse" x="1016.032" y="1820.129" width="1391.549" height="197.113">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1016.032" y="1820.129" width="1391.549" height="197.113" id="SVGID_00000033346773201402994670000017547449945181607854_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000003813385926652374230000005625262885488300419_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000073715532954461063320000013568279248423864470_" filterUnits="userSpaceOnUse" x="1016.032" y="1820.129" width="1391.549" height="197.113">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1016.032" y="1820.129" width="1391.549" height="197.113" id="SVGID_00000033346773201402994670000017547449945181607854_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000073715532954461063320000013568279248423864470_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000071547967995026793170000014575565731740686468_" gradientUnits="userSpaceOnUse" x1="1711.8066" y1="2017.2419" x2="1711.8066" y2="1820.1285">
									<stop offset="0.043" style="stop-color:#4D4D4D"/>
									<stop offset="0.0649" style="stop-color:#646464"/>
									<stop offset="0.1049" style="stop-color:#888888"/>
									<stop offset="0.15" style="stop-color:#A8A8A8"/>
									<stop offset="0.1998" style="stop-color:#C4C4C4"/>
									<stop offset="0.256" style="stop-color:#DADADA"/>
									<stop offset="0.3217" style="stop-color:#EBEBEB"/>
									<stop offset="0.403" style="stop-color:#F6F6F6"/>
									<stop offset="0.517" style="stop-color:#FDFDFD"/>
									<stop offset="0.8333" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<polygon style="mask:url(#SVGID_00000033346773201402994670000017547449945181607854_);fill:url(#SVGID_00000071547967995026793170000014575565731740686468_);" points="          2407.581,1903.747 2407.581,2017.242 1016.032,2017.242 1016.032,1820.129 2406.841,1820.129 2406.841,1903.683           2376.555,1903.683 2376.555,1903.747         "/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000160898289810604140970000005435058637375698840_" gradientUnits="userSpaceOnUse" x1="1711.8066" y1="2017.2419" x2="1711.8066" y2="1820.1285">
							<stop offset="0.043" style="stop-color:#FFFFFF"/>
							<stop offset="0.0624" style="stop-color:#E5E5E5"/>
							<stop offset="0.0977" style="stop-color:#BDBCBC"/>
							<stop offset="0.1373" style="stop-color:#989697"/>
							<stop offset="0.1803" style="stop-color:#787676"/>
							<stop offset="0.228" style="stop-color:#5D5A5B"/>
							<stop offset="0.2817" style="stop-color:#484445"/>
							<stop offset="0.3445" style="stop-color:#373334"/>
							<stop offset="0.4222" style="stop-color:#2C2829"/>
							<stop offset="0.5311" style="stop-color:#252122"/>
							<stop offset="0.8333" style="stop-color:#231F20"/>
						</linearGradient>
						<polygon style="opacity:0.1;fill:url(#SVGID_00000160898289810604140970000005435058637375698840_);" points="        2407.581,1903.747 2407.581,2017.242 1016.032,2017.242 1016.032,1820.129 2406.841,1820.129 2406.841,1903.683         2376.555,1903.683 2376.555,1903.747       "/>
						<defs>
							
								<filter id="Adobe_OpacityMaskFilter_00000169559527739494589860000003167413482259486119_" filterUnits="userSpaceOnUse" x="1016.028" y="4418.427" width="1391.55" height="235.487">
								<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
								<feBlend in="SourceGraphic" in2="back" mode="normal"/>
							</filter>
						</defs>
						
							<mask maskUnits="userSpaceOnUse" x="1016.028" y="4418.427" width="1391.55" height="235.487" id="SVGID_00000183245240236053670610000012741497103314674329_">
							<g style="filter:url(#Adobe_OpacityMaskFilter_00000169559527739494589860000003167413482259486119_);">
								<defs>
									
										<filter id="Adobe_OpacityMaskFilter_00000150791361587493706180000014980459233197988532_" filterUnits="userSpaceOnUse" x="1016.028" y="4418.427" width="1391.55" height="235.487">
										<feFlood style="flood-color:white;flood-opacity:1" result="back"/>
										<feBlend in="SourceGraphic" in2="back" mode="normal"/>
									</filter>
								</defs>
								
									<mask maskUnits="userSpaceOnUse" x="1016.028" y="4418.427" width="1391.55" height="235.487" id="SVGID_00000183245240236053670610000012741497103314674329_">
									<g style="filter:url(#Adobe_OpacityMaskFilter_00000150791361587493706180000014980459233197988532_);">
									</g>
								</mask>
								
									<linearGradient id="SVGID_00000024691239886780937530000008967937022786446236_" gradientUnits="userSpaceOnUse" x1="-5142.9331" y1="-3303.781" x2="-5142.9331" y2="-3539.2683" gradientTransform="matrix(-1 0 0 -1 -3431.1301 1114.646)">
									<stop offset="0.043" style="stop-color:#4D4D4D"/>
									<stop offset="0.0649" style="stop-color:#646464"/>
									<stop offset="0.1049" style="stop-color:#888888"/>
									<stop offset="0.15" style="stop-color:#A8A8A8"/>
									<stop offset="0.1998" style="stop-color:#C4C4C4"/>
									<stop offset="0.256" style="stop-color:#DADADA"/>
									<stop offset="0.3217" style="stop-color:#EBEBEB"/>
									<stop offset="0.403" style="stop-color:#F6F6F6"/>
									<stop offset="0.517" style="stop-color:#FDFDFD"/>
									<stop offset="0.8333" style="stop-color:#FFFFFF"/>
								</linearGradient>
								
									<polygon style="mask:url(#SVGID_00000183245240236053670610000012741497103314674329_);fill:url(#SVGID_00000024691239886780937530000008967937022786446236_);" points="          1016.028,4554.018 1016.028,4418.427 2407.578,4418.427 2407.578,4653.914 1016.769,4653.914 1016.769,4554.094           1047.055,4554.094 1047.055,4554.018         "/>
							</g>
						</mask>
						
							<linearGradient id="SVGID_00000183240336895317921260000003141200005265932945_" gradientUnits="userSpaceOnUse" x1="-5142.9331" y1="-3303.781" x2="-5142.9331" y2="-3539.2683" gradientTransform="matrix(-1 0 0 -1 -3431.1301 1114.646)">
							<stop offset="0.043" style="stop-color:#FFFFFF"/>
							<stop offset="0.0624" style="stop-color:#E5E5E5"/>
							<stop offset="0.0977" style="stop-color:#BDBCBC"/>
							<stop offset="0.1373" style="stop-color:#989697"/>
							<stop offset="0.1803" style="stop-color:#787676"/>
							<stop offset="0.228" style="stop-color:#5D5A5B"/>
							<stop offset="0.2817" style="stop-color:#484445"/>
							<stop offset="0.3445" style="stop-color:#373334"/>
							<stop offset="0.4222" style="stop-color:#2C2829"/>
							<stop offset="0.5311" style="stop-color:#252122"/>
							<stop offset="0.8333" style="stop-color:#231F20"/>
						</linearGradient>
						<polygon style="opacity:0.1;fill:url(#SVGID_00000183240336895317921260000003141200005265932945_);" points="        1016.028,4554.018 1016.028,4418.427 2407.578,4418.427 2407.578,4653.914 1016.769,4653.914 1016.769,4554.094         1047.055,4554.094 1047.055,4554.018       "/>
					</g>
				</g>
			</g>
		</g>
	</g>
</g>
</svg>
    </button>
</template>
