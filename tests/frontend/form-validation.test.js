import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import WebcamApp from '../../resources/js/components/WebcamApp.vue';

describe('Form Validation', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = mount(WebcamApp);
  });

  describe('Login Form', () => {
    beforeEach(async () => {
      wrapper.vm.authMode = 'login';
      await wrapper.vm.$nextTick();
    });

    it('should render login form with required fields', () => {
      const emailInput = wrapper.find('input[type="email"]');
      const passwordInput = wrapper.find('input[type="password"]');
      const submitButton = wrapper.find('button[type="submit"]');

      expect(emailInput.exists()).toBe(true);
      expect(passwordInput.exists()).toBe(true);
      expect(submitButton.exists()).toBe(true);
      
      expect(emailInput.attributes('required')).toBeDefined();
      expect(passwordInput.attributes('required')).toBeDefined();
    });

    it('should update login form data when inputs change', async () => {
      const emailInput = wrapper.find('input[type="email"]');
      const passwordInput = wrapper.find('input[type="password"]');

      await emailInput.setValue('test@example.com');
      await passwordInput.setValue('password123');

      expect(wrapper.vm.loginForm.email).toBe('test@example.com');
      expect(wrapper.vm.loginForm.password).toBe('password123');
    });

    it('should handle remember me checkbox', async () => {
      const checkbox = wrapper.find('input[type="checkbox"]');
      
      expect(wrapper.vm.loginForm.remember).toBe(false);
      
      await checkbox.setChecked(true);
      expect(wrapper.vm.loginForm.remember).toBe(true);
    });

    it('should call login method on form submission', async () => {
      // Mock the fetch function to simulate API call
      const mockFetch = vi.fn().mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({
          success: true,
          message: 'Login successful',
          user: { id: 1, name: 'Test User', email: 'test@example.com' }
        })
      });
      global.fetch = mockFetch;
      
      await wrapper.find('input[type="email"]').setValue('test@example.com');
      await wrapper.find('input[type="password"]').setValue('password123');
      
      const form = wrapper.find('form');
      await form.trigger('submit.prevent');
      
      // Wait for the login API call to be made
      await wrapper.vm.$nextTick();

      // Check that fetch was called with login endpoint
      expect(mockFetch).toHaveBeenCalledWith('/api/auth/login', expect.objectContaining({
        method: 'POST',
        headers: expect.objectContaining({
          'Content-Type': 'application/json'
        }),
        body: JSON.stringify({
          email: 'test@example.com',
          password: 'password123',
          remember: false
        })
      }));
    });
  });

  describe('Registration Form', () => {
    beforeEach(async () => {
      wrapper.vm.authMode = 'register';
      await wrapper.vm.$nextTick();
    });

    it('should render registration form with all required fields', () => {
      const nameInput = wrapper.find('input[placeholder="John Doe"]');
      const emailInput = wrapper.find('input[placeholder="your@email.com"]');
      const passwordInputs = wrapper.findAll('input[type="password"]');

      expect(nameInput.exists()).toBe(true);
      expect(emailInput.exists()).toBe(true);
      expect(passwordInputs).toHaveLength(2);
      
      expect(nameInput.attributes('required')).toBeDefined();
      expect(emailInput.attributes('required')).toBeDefined();
    });

    it('should enforce minimum password length', () => {
      const passwordInput = wrapper.find('input[minlength="8"]');
      expect(passwordInput.exists()).toBe(true);
      expect(passwordInput.attributes('minlength')).toBe('8');
    });

    it('should update registration form data', async () => {
      const nameInput = wrapper.find('input[placeholder="John Doe"]');
      const emailInput = wrapper.find('input[placeholder="your@email.com"]');
      const passwordInputs = wrapper.findAll('input[type="password"]');

      await nameInput.setValue('John Doe');
      await emailInput.setValue('john@example.com');
      await passwordInputs[0].setValue('password123');
      await passwordInputs[1].setValue('password123');

      expect(wrapper.vm.registerForm.name).toBe('John Doe');
      expect(wrapper.vm.registerForm.email).toBe('john@example.com');
      expect(wrapper.vm.registerForm.password).toBe('password123');
      expect(wrapper.vm.registerForm.password_confirmation).toBe('password123');
    });
  });

  describe('Form Toggle', () => {
    it('should switch between login and register modes', async () => {
      expect(wrapper.vm.authMode).toBe('login');

      wrapper.vm.authMode = 'register';
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.authMode).toBe('register');
    });
  });

  describe('Loading States', () => {
    it('should disable submit button during loading', async () => {
      wrapper.vm.authLoading = true;
      await wrapper.vm.$nextTick();

      const submitButton = wrapper.find('button[type="submit"]');
      expect(submitButton.attributes('disabled')).toBeDefined();
    });
  });
}); 