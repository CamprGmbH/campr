<template>
    <div class="reschedule-icon icon">
        <svg version="1.1" v-bind:class="fill" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16">
            <path d="M9,4.1c-0.3,0-0.5,0.2-0.5,0.5v3.8c0,0.3,0.2,0.5,0.5,0.5h3.4c0.3,0,0.5-0.2,0.5-0.5s-0.2-0.5-0.5-0.5H9.5V4.6
				C9.5,4.3,9.3,4.1,9,4.1z"/>
			<path d="M9,1C5.4,1,2.4,3.8,2.1,7.4L0.9,5.7C0.8,5.4,0.5,5.4,0.2,5.5C0,5.7-0.1,6,0.1,6.2l2,3.1c0,0,0,0,0,0c0,0,0.1,0.1,0.1,0.1
				c0,0,0,0,0.1,0c0,0,0.1,0,0.1,0c0,0,0,0,0.1,0c0,0,0,0,0,0c0.1,0,0.1,0,0.2,0c0,0,0,0,0,0c0,0,0.1-0.1,0.1-0.1c0,0,0,0,0,0l2.4-2.7
				c0.2-0.2,0.2-0.5,0-0.7C5.1,5.7,4.7,5.8,4.6,6L3.1,7.7C3.2,4.5,5.8,2,9,2c3.3,0,6,2.7,6,6c0,3.3-2.7,6-6,6c-0.3,0-0.5,0.2-0.5,0.5
				S8.7,15,9,15c3.8,0,7-3.1,7-7S12.9,1,9,1z"/>
        </svg>
    </div>
</template>

<script>
export default {
    props: ['fill'],
};
</script>
