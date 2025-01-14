<template>
  <div class="custom-datepicker">
    <input
        type="date"
        :id="id"
        :name="name"
        class="form-control"
        :value="computedValue"
        @input="updateValue($event.target.value)"
        :required="required"
        :min="minDate"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'CustomDatepicker',
  props: {
    modelValue: {
      type: String,
      required: false,
    },
    id: {
      type: String,
      default: '',
    },
    name: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue'],
  computed: {
    computedValue: {
      get(): string | undefined {
        return this.modelValue;
      },
      set(value: string) {
        this.$emit('update:modelValue', value);
      },
    },
    minDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = (today.getMonth() + 1).toString().padStart(2, '0');
      const day = today.getDate().toString().padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
  },
  methods: {
    updateValue(value: string) {
      this.$emit('update:modelValue', value);
    },
  },
});
</script>

<style scoped>
.custom-datepicker {
  margin-bottom: 1rem;
}
</style>
