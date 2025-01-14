<template>
  <div class="custom-file-input">
    <input
        type="file"
        :id="id"
        :name="name"
        class="form-control"
        @change="onFileChange"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'CustomFileInput',
  props: {
    id: {
      type: String,
      default: '',
    },
    name: {
      type: String,
      default: '',
    },
  },
  methods: {
    onFileChange(event: Event) {
      const fileInput = event.target as HTMLInputElement;
      const files = fileInput?.files;
      if (files && files.length > 0) {
        this.$emit('update:modelValue', files[0]);
      }
    },
  },
});
</script>

<style scoped>
.custom-file-input {
  margin-bottom: 1rem;
}
</style>
