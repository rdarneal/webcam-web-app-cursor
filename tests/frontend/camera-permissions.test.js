import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import WebcamApp from '../../resources/js/components/WebcamApp.vue';

describe('Camera Permissions and Functionality', () => {
  let wrapper;
  let mockGetUserMedia;
  let mockVideoElement;
  let mockCanvasElement;

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

    mockGetUserMedia = vi.fn();
    mockVideoElement = {
      srcObject: null,
      videoWidth: 1280,
      videoHeight: 720,
      play: vi.fn(),
      pause: vi.fn(),
    };

    mockCanvasElement = {
      width: 0,
      height: 0,
      getContext: vi.fn(() => ({
        drawImage: vi.fn(function(image, sx, sy, sw, sh, dx, dy, dw, dh) {
          // Mock successful drawImage operation
          console.log('Mock drawImage called with:', arguments.length, 'arguments');
        }),
      })),
      toBlob: vi.fn((callback, type, quality) => {
        console.log('Default toBlob mock called - this should be overridden');
        callback(new Blob(['test'], { type: type || 'image/jpeg' }));
      }),
      toDataURL: vi.fn(() => 'data:image/jpeg;base64,mock'),
    };

    global.navigator.mediaDevices.getUserMedia = mockGetUserMedia;
    
    // Mock fetch for auth calls that happen on mount
    const mockFetch = vi.fn()
      .mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ authenticated: false })
      });
    global.fetch = mockFetch;
    
    wrapper = mount(WebcamApp);
    
    // Wait for component to finish mounting
    await wrapper.vm.$nextTick();
    
    // Override the refs properly for Vue 3 Composition API
    wrapper.vm.videoElement = mockVideoElement;
    wrapper.vm.canvasElement = mockCanvasElement;
  });

  describe('Camera Support Detection', () => {
    it('should detect when camera is not supported', async () => {
      delete global.navigator.mediaDevices.getUserMedia;
      
      const newWrapper = mount(WebcamApp);
      await newWrapper.vm.$nextTick();

      expect(newWrapper.vm.statusMessage).toContain('Webcam access is not supported');
      expect(newWrapper.vm.statusType).toBe('error');
    });
  });

  describe('Camera Initialization', () => {
    it('should start camera successfully', async () => {
      const mockStream = {
        getTracks: vi.fn(() => [{ stop: vi.fn() }])
      };
      
      mockGetUserMedia.mockResolvedValueOnce(mockStream);

      await wrapper.vm.startCamera();

      expect(mockGetUserMedia).toHaveBeenCalledWith({
        video: {
          width: { ideal: 1280 },
          height: { ideal: 720 },
          facingMode: 'user'
        },
        audio: false
      });

      expect(mockVideoElement.srcObject).toBe(mockStream);
      expect(wrapper.vm.isVideoActive).toBe(true);
    });

    it('should handle camera permission denial', async () => {
      const permissionError = new Error('Permission denied');
      permissionError.name = 'NotAllowedError';
      
      mockGetUserMedia.mockRejectedValueOnce(permissionError);

      await wrapper.vm.startCamera();

      expect(wrapper.vm.isVideoActive).toBe(false);
      expect(wrapper.vm.statusMessage).toBe('Failed to access camera. Please check permissions.');
    });
  });

  describe('Camera Controls', () => {
    it('should toggle camera on and off', async () => {
      const mockStream = {
        getTracks: vi.fn(() => [{ stop: vi.fn() }])
      };
      
      mockGetUserMedia.mockResolvedValueOnce(mockStream);

      expect(wrapper.vm.isVideoActive).toBe(false);
      await wrapper.vm.toggleCamera();
      expect(wrapper.vm.isVideoActive).toBe(true);

      await wrapper.vm.toggleCamera();
      expect(wrapper.vm.isVideoActive).toBe(false);
    });

    it('should cleanup resources on stop', () => {
      const mockTrack = { stop: vi.fn() };
      const mockStream = {
        getTracks: vi.fn(() => [mockTrack])
      };

      wrapper.vm.isVideoActive = true;
      wrapper.vm.mediaStream = mockStream;

      wrapper.vm.stopCamera();

      expect(mockTrack.stop).toHaveBeenCalled();
      expect(wrapper.vm.isVideoActive).toBe(false);
    });
  });

  describe('Photo Capture', () => {
    beforeEach(() => {
      wrapper.vm.isVideoActive = true;
      wrapper.vm.user = {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        has_openai_key: true,
        has_elevenlabs_key: true
      };
    });

    it('should capture photo when camera is active', async () => {
      // Ensure all conditions are met
      expect(wrapper.vm.isVideoActive).toBe(true);
      expect(wrapper.vm.hasValidApiKeys).toBe(true);
      expect(wrapper.vm.videoElement).toBeTruthy();
      expect(wrapper.vm.canvasElement).toBeTruthy();
      
      // Mock processPhotoBlob to avoid API calls in tests
      const mockProcessPhotoBlob = vi.spyOn(wrapper.vm, 'processPhotoBlob').mockResolvedValue();

      // Verify starting state
      const initialPhotoCount = wrapper.vm.capturedPhotos.length;

      // Capture a photo
      await wrapper.vm.capturePhoto();

      // Verify the photo was added to the array
      expect(wrapper.vm.capturedPhotos.length).toBe(initialPhotoCount + 1);
      
      // Verify photo has expected properties
      const capturedPhoto = wrapper.vm.capturedPhotos[0];
      expect(capturedPhoto).toHaveProperty('dataUrl');
      expect(capturedPhoto).toHaveProperty('timestamp');
      expect(capturedPhoto).toHaveProperty('processed');
      expect(capturedPhoto.dataUrl).toMatch(/^data:image\/jpeg;base64,/);
    });

    it('should not capture photo when camera is inactive', async () => {
      wrapper.vm.isVideoActive = false;

      await wrapper.vm.capturePhoto();

      expect(wrapper.vm.capturedPhotos.length).toBe(0);
    });

    it('should limit captured photos to 10', async () => {
      const mockBlob = new Blob(['test'], { type: 'image/jpeg' });
      vi.spyOn(wrapper.vm, 'processPhotoBlob').mockResolvedValue();

      // Mock canvas.toBlob to immediately call the callback
      mockCanvasElement.toBlob = vi.fn((callback, type, quality) => {
        const mockBlob = new Blob(['test'], { type: 'image/jpeg' });
        callback(mockBlob);
      });

      for (let i = 0; i < 12; i++) {
        await wrapper.vm.capturePhoto();
      }

      expect(wrapper.vm.capturedPhotos.length).toBe(10);
    });
  });

  describe('Cleanup', () => {
    it('should stop camera when component is unmounted', () => {
      const mockTrack = { stop: vi.fn() };
      const mockStream = {
        getTracks: vi.fn(() => [mockTrack])
      };

      wrapper.vm.isVideoActive = true;
      wrapper.vm.mediaStream = mockStream;

      wrapper.unmount();

      expect(mockTrack.stop).toHaveBeenCalled();
    });
  });
}); 