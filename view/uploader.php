<?php
defined( "ABSPATH" ) || exit;
?>
<div id="nixfile-box">
    <div class="nixfile-errors-box"></div>
    <form>
        <label id="nixfile-uploader-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <!-- Icon from Tabler Icons by Paweł Kuna - https://github.com/tabler/tabler-icons/blob/master/LICENSE -->
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 9l5-5l5 5m-5-5v12"/>
            </svg>
            <input multiple id="nixfile-uploader-input" type="file">
        </label>
    </form>
</div>

