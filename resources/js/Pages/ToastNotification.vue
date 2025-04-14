<template>
    <div v-if="visible" class="toast" :class="toastClass">
      <div class="toast-body">
        {{ message }}
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        visible: false,
        message: '',
        toastClass: 'toast-success'
      };
    },
    methods: {
      showToast(message, success = true) {
        this.message = message;
        this.toastClass = success ? 'toast-success' : 'toast-error';
        this.visible = true;
        setTimeout(() => { this.visible = false; }, 3000);
      }
    }
  };
  </script>
  
  <style scoped>
  .toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px 20px;
    border-radius: 5px;
    color: white;
  }
  
  .toast-success {
    background-color: green;
  }
  
  .toast-error {
    background-color: red;
  }
  </style>
  