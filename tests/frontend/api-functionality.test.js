import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import WebcamApp from '../../resources/js/components/WebcamApp.vue';

describe('API Functionality', () => {
  let wrapper;
  let mockFetch;

  beforeEach(async () => {
    // Mock CSRF token in document
    Object.defineProperty(document, 'querySelector', {
      value: vi.fn((selector) => {
        if (selector === 'meta[name="csrf-token"]') {
          return { getAttribute: () => 'mock-csrf-token' };
        }
        return null;
      }),
      writable: true
    });

    mockFetch = vi.fn();
    
    // Mock auth check calls that happen on component mount
    mockFetch
      .mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ authenticated: true, user: { id: 1 } })
      })
      .mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ 
          success: true, 
          user: {
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            has_openai_key: false,
            has_elevenlabs_key: false
          }
        })
      });
    
    global.fetch = mockFetch;
    
    wrapper = mount(WebcamApp);
    
    // Wait for component to finish mounting and auth calls to complete
    await wrapper.vm.$nextTick();
    
    // Clear the mock calls from component mount so tests start fresh
    mockFetch.mockClear();
    
    // Set authenticated state
    wrapper.vm.isAuthenticated = true;
    wrapper.vm.user = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      has_openai_key: false,
      has_elevenlabs_key: false
    };
  });

  describe('API Key Management', () => {
    describe('Save API Keys', () => {
      it('should save all API keys successfully', async () => {
        const mockResponse = {
          success: true,
          message: 'API keys saved successfully'
        };
        
        // Mock the main API keys save call
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        // Mock the fetchUser call that happens after successful save
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve({ 
            success: true, 
            user: {
              id: 1,
              name: 'John Doe',
              email: 'john@example.com',
              has_openai_key: true,
              has_elevenlabs_key: true
            }
          })
        });

        wrapper.vm.apiKeyForm = {
          openai_key: 'sk-test123',
          elevenlabs_key: 'el-test456'
        };

        await wrapper.vm.saveApiKeys();

        expect(mockFetch).toHaveBeenNthCalledWith(2, '/api/api-keys', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': 'mock-csrf-token'
          },
          body: JSON.stringify({
            openai_key: 'sk-test123',
            elevenlabs_key: 'el-test456'
          })
        });

        expect(wrapper.vm.apiKeyMessage).toBe(mockResponse.message);
        expect(wrapper.vm.apiKeyMessageType).toBe('success');
        expect(wrapper.vm.apiKeyForm.openai_key).toBe('');
        expect(wrapper.vm.apiKeyForm.elevenlabs_key).toBe('');
      });

      it('should handle API key save failure', async () => {
        const mockResponse = {
          success: false,
          message: 'Invalid API key format'
        };
        
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        await wrapper.vm.saveApiKeys();

        expect(wrapper.vm.apiKeyMessage).toBe(mockResponse.message);
        expect(wrapper.vm.apiKeyMessageType).toBe('error');
      });
    });

    describe('Individual API Key Operations', () => {
      it('should save individual OpenAI key', async () => {
        const mockResponse = {
          success: true,
          message: 'OpenAI key saved successfully'
        };
        
        // Mock the individual save call
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        // Mock the fetchUser call that happens after successful save
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve({ 
            success: true, 
            user: {
              id: 1,
              name: 'John Doe',
              email: 'john@example.com',
              has_openai_key: true,
              has_elevenlabs_key: false
            }
          })
        });

        wrapper.vm.apiKeyForm.openai_key = 'sk-test123';

        await wrapper.vm.saveIndividualKey('openai');

        expect(mockFetch).toHaveBeenCalledWith('/api/api-keys/openai', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': 'mock-csrf-token'
          },
          body: JSON.stringify({ openai_key: 'sk-test123' })
        });

        expect(wrapper.vm.openaiStatus).toEqual({
          type: 'success',
          message: mockResponse.message
        });
        expect(wrapper.vm.apiKeyForm.openai_key).toBe('');
      });

      it('should save individual ElevenLabs key', async () => {
        const mockResponse = {
          success: true,
          message: 'ElevenLabs key saved successfully'
        };
        
        // Mock the individual save call
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        // Mock the fetchUser call that happens after successful save
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve({ 
            success: true, 
            user: {
              id: 1,
              name: 'John Doe',
              email: 'john@example.com',
              has_openai_key: false,
              has_elevenlabs_key: true
            }
          })
        });

        wrapper.vm.apiKeyForm.elevenlabs_key = 'el-test456';

        await wrapper.vm.saveIndividualKey('elevenlabs');

        expect(mockFetch).toHaveBeenCalledWith('/api/api-keys/elevenlabs', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': 'mock-csrf-token'
          },
          body: JSON.stringify({ elevenlabs_key: 'el-test456' })
        });

        expect(wrapper.vm.elevenLabsStatus).toEqual({
          type: 'success',
          message: mockResponse.message
        });
      });

      it('should test individual API keys', async () => {
        const mockResponse = {
          success: true,
          message: 'API key is valid'
        };
        
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        await wrapper.vm.testIndividualKey('openai');

        expect(mockFetch).toHaveBeenCalledWith('/api/api-keys/openai/test', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': 'mock-csrf-token'
          }
        });

        expect(wrapper.vm.openaiStatus).toEqual({
          type: 'success',
          message: mockResponse.message
        });
      });
    });

    describe('Validate API Keys', () => {
      it('should validate all API keys', async () => {
        const mockResponse = {
          success: true,
          message: 'All API keys are valid'
        };
        
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        await wrapper.vm.validateApiKeys();

        expect(mockFetch).toHaveBeenCalledWith('/api/api-keys/validate', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': 'mock-csrf-token'
          }
        });

        expect(wrapper.vm.apiKeyMessage).toBe(mockResponse.message);
        expect(wrapper.vm.apiKeyMessageType).toBe('success');
      });

      it('should handle validation failure', async () => {
        const mockResponse = {
          success: false,
          message: 'OpenAI key is invalid'
        };
        
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        await wrapper.vm.validateApiKeys();

        expect(wrapper.vm.apiKeyMessageType).toBe('error');
      });
    });

    describe('Delete API Keys', () => {
      it('should delete all API keys after confirmation', async () => {
        const mockResponse = {
          success: true,
          message: 'All API keys deleted'
        };
        
        // Mock the delete call
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve(mockResponse)
        });

        // Mock the fetchUser call that happens after successful delete
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve({ 
            success: true, 
            user: {
              id: 1,
              name: 'John Doe',
              email: 'john@example.com',
              has_openai_key: false,
              has_elevenlabs_key: false
            }
          })
        });

        // Mock confirm dialog
        global.confirm = vi.fn(() => true);

        await wrapper.vm.deleteApiKeys();

        expect(mockFetch).toHaveBeenCalledWith('/api/api-keys', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': 'mock-csrf-token'
          },
          body: JSON.stringify({
            services: ['openai', 'elevenlabs']
          })
        });

        expect(wrapper.vm.apiKeyMessage).toBe(mockResponse.message);
        expect(wrapper.vm.apiKeyMessageType).toBe('success');
      });

      it('should cancel deletion if user does not confirm', async () => {
        // Mock the fetchUser call that may happen due to reactive user state changes
        mockFetch.mockResolvedValueOnce({
          ok: true,
          json: () => Promise.resolve({ 
            success: true, 
            user: wrapper.vm.user
          })
        });

        global.confirm = vi.fn(() => false);

        await wrapper.vm.deleteApiKeys();

        // Only expect the fetchUser call, not the delete call
        expect(mockFetch).toHaveBeenCalledTimes(1);
        expect(mockFetch).toHaveBeenCalledWith('/api/auth/user');
      });
    });
  });

  describe('Image Processing API', () => {
    it('should process image successfully', async () => {
      const mockResponse = {
        success: true,
        audio_url: '/storage/audio/test.mp3'
      };
      
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(mockResponse)
      });

      // Mock user has valid API keys
      wrapper.vm.user.has_openai_key = true;
      wrapper.vm.user.has_elevenlabs_key = true;

      const mockBlob = new Blob(['test image'], { type: 'image/jpeg' });
      const mockPhoto = { dataUrl: 'data:image/jpeg;base64,test', timestamp: Date.now() };

      await wrapper.vm.processPhotoBlob(mockBlob, mockPhoto);

      expect(mockFetch).toHaveBeenCalledWith('/api/process-image-proxy', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': 'mock-csrf-token'
        },
        body: expect.any(FormData)
      });

      expect(wrapper.vm.audioUrl).toBe(mockResponse.audio_url);
      expect(wrapper.vm.apiError).toBe(null);
      expect(mockPhoto.processed).toBe(true);
    });

    it('should handle API errors with structured error response', async () => {
      const mockResponse = {
        success: false,
        error: 'Processing failed',
        api_error: {
          service: 'openai',
          type: 'insufficient_quota',
          message: 'You have exceeded your quota'
        }
      };
      
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(mockResponse)
      });

      wrapper.vm.user.has_openai_key = true;
      wrapper.vm.user.has_elevenlabs_key = true;

      const mockBlob = new Blob(['test image'], { type: 'image/jpeg' });
      const mockPhoto = { dataUrl: 'data:image/jpeg;base64,test', timestamp: Date.now() };

      await wrapper.vm.processPhotoBlob(mockBlob, mockPhoto);

      expect(wrapper.vm.apiError).toEqual(mockResponse.api_error);
      expect(wrapper.vm.statusType).toBe('error');
    });

    it('should prevent processing without valid API keys', async () => {
      // Mock the fetchUser call that may happen when we change user state
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ 
          success: true, 
          user: {
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            has_openai_key: false,
            has_elevenlabs_key: false
          }
        })
      });

      wrapper.vm.user.has_openai_key = false;
      wrapper.vm.user.has_elevenlabs_key = false;

      const mockBlob = new Blob(['test image'], { type: 'image/jpeg' });
      const mockPhoto = { dataUrl: 'data:image/jpeg;base64,test', timestamp: Date.now() };

      await wrapper.vm.processPhotoBlob(mockBlob, mockPhoto);

      // Should only have the fetchUser call, not the processing call
      expect(mockFetch).toHaveBeenCalledTimes(1);
      expect(mockFetch).toHaveBeenCalledWith('/api/auth/user');
      expect(wrapper.vm.statusMessage).toBe('Please configure your API keys first');
      expect(wrapper.vm.statusType).toBe('error');
    });
  });

  describe('Error Suggestions', () => {
    it('should provide helpful suggestions for OpenAI errors', () => {
      const quotaError = {
        service: 'openai',
        type: 'insufficient_quota',
        message: 'You have exceeded your quota'
      };

      const suggestion = wrapper.vm.getErrorSuggestion(quotaError);
      expect(suggestion).toContain('Check your OpenAI billing');
      expect(suggestion).toContain('add credits');
    });

    it('should provide helpful suggestions for ElevenLabs errors', () => {
      const quotaError = {
        service: 'elevenlabs',
        type: 'quota_exceeded',
        message: 'Character limit reached'
      };

      const suggestion = wrapper.vm.getErrorSuggestion(quotaError);
      expect(suggestion).toContain('character limit');
      expect(suggestion).toContain('Upgrade your plan');
    });

    it('should handle unknown error types', () => {
      const unknownError = {
        service: 'openai',
        type: 'unknown_error',
        message: 'Something went wrong'
      };

      const suggestion = wrapper.vm.getErrorSuggestion(unknownError);
      expect(suggestion).toContain('Check your OpenAI API key');
    });
  });

  describe('Loading States', () => {
    it('should show loading state during API operations', async () => {
      wrapper.vm.apiKeyLoading = true;
      await wrapper.vm.$nextTick();

      expect(wrapper.vm.apiKeyLoading).toBe(true);
    });

    it('should show processing state during image processing', async () => {
      wrapper.vm.isProcessing = true;
      wrapper.vm.processingStatus = 'Analyzing image...';
      await wrapper.vm.$nextTick();

      expect(wrapper.vm.isProcessing).toBe(true);
      expect(wrapper.vm.processingStatus).toBe('Analyzing image...');
    });
  });
}); 