import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import WebcamApp from '../../resources/js/components/WebcamApp.vue';

describe('Local Storage Integration', () => {
  let wrapper;
  let mockLocalStorage;

  beforeEach(() => {
    mockLocalStorage = {
      getItem: vi.fn(),
      setItem: vi.fn(),
      removeItem: vi.fn(),
      clear: vi.fn(),
    };
    global.localStorage = mockLocalStorage;
    wrapper = mount(WebcamApp);
  });

  describe('Advanced Settings Persistence', () => {
    it('should load advanced settings from localStorage', () => {
      const mockSettings = {
        gptModel: 'gpt-4o',
        promptMessage: 'Custom prompt',
        voiceId: 'TestVoice',
        customVoiceId: 'Custom'
      };

      mockLocalStorage.getItem.mockReturnValueOnce(JSON.stringify(mockSettings));
      wrapper.vm.loadAdvancedSettings();

      expect(wrapper.vm.advancedSettings).toMatchObject(mockSettings);
    });

    it('should save advanced settings to localStorage', () => {
      const testSettings = {
        gptModel: 'gpt-4o',
        promptMessage: 'Test prompt',
        voiceId: 'TestVoice',
        customVoiceId: 'Custom'
      };

      wrapper.vm.advancedSettings = testSettings;
      wrapper.vm.saveAdvancedSettings();

      expect(mockLocalStorage.setItem).toHaveBeenCalledWith(
        'webcam-assistant-advanced',
        JSON.stringify(testSettings)
      );
    });

    it('should reset settings and clear localStorage', () => {
      wrapper.vm.resetAdvancedSettings();

      expect(wrapper.vm.advancedSettings.gptModel).toBe('gpt-4o-mini');
      expect(mockLocalStorage.removeItem).toHaveBeenCalledWith('webcam-assistant-advanced');
    });
  });
}); 