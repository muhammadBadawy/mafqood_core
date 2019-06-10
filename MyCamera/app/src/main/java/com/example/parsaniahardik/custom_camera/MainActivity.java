package com.example.parsaniahardik.custom_camera;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.hardware.Camera;
import android.media.MediaScannerConnection;
import android.os.Environment;
import android.os.Bundle;
import android.util.Log;
import android.view.WindowManager;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Calendar;
import android.Manifest;
import android.content.pm.PackageManager;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.AppCompatActivity;
import android.widget.Toast;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.location.LocationManager;
import android.location.LocationListener;
import android.location.Location;

public class MainActivity extends AppCompatActivity {
    private static final String IMAGE_DIRECTORY = "/CustomImage";

    private Camera mCamera;
    private CameraPreview mPreview;
    private Camera.PictureCallback mPicture;
    private Context myContext;
    private LinearLayout cameraPreview;
    private boolean cameraFront = false;
    public static Bitmap bitmap;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        Bundle bundle=getIntent().getExtras();

        String phone = bundle.getString("Phone");
        String email = bundle.getString("Email");
        String district = bundle.getString("District");
        Toast.makeText(getApplicationContext(),phone+email+district,Toast.LENGTH_SHORT).show();

        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        myContext = this;
        int cameraId = findBackFacingCamera();
            mCamera = Camera.open(cameraId);
            mCamera.setDisplayOrientation(90);
            mPicture = getPictureCallback();

        cameraPreview = (LinearLayout) findViewById(R.id.cPreview);
        mPreview = new CameraPreview(myContext, mCamera);
        cameraPreview.addView(mPreview);

        Thread thread = new Thread() {
            @Override
            public void run() {
                try {
                    while(true) {
                        sleep(5000);
                        mCamera.takePicture(null, null, mPicture);
                    }
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            }
        };

        thread.start();

        mCamera.startPreview();

        // Get user location
        LocationManager locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            // TODO: Consider calling
            return;
        }
        locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 5000, 10, new Listener());
        // Have another for GPS provider just in case.
        locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 5000, 10, new Listener());
        // Try to request the location immediately
        Location location = locationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);
        if (location == null){
            location = locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER);
        }
        if (location != null){
            handleLatLng(location.getLatitude(), location.getLongitude());
        }
    }


    /**
     * Handle lat lng.
     */
    private void handleLatLng(double latitude, double longitude){

        Toast.makeText(getApplicationContext(),
                "(" + latitude + "," + longitude + ")",
                Toast.LENGTH_SHORT).show();

    }

    /**
     * Listener for changing gps coords.
     */
    private class Listener implements LocationListener {
        public void onLocationChanged(Location location) {
            double longitude = location.getLongitude();
            double latitude = location.getLatitude();
            handleLatLng(latitude, longitude);
        }

        public void onProviderDisabled(String provider){}
        public void onProviderEnabled(String provider){}
        public void onStatusChanged(String provider, int status, Bundle extras){}
    }


    private int findBackFacingCamera() {
        int cameraId = -1;
        //Search for the back facing camera
        //get the number of cameras
        int numberOfCameras = Camera.getNumberOfCameras();
        //for every camera check
        for (int i = 0; i < numberOfCameras; i++) {
            Camera.CameraInfo info = new Camera.CameraInfo();
            Camera.getCameraInfo(i, info);
            if (info.facing == Camera.CameraInfo.CAMERA_FACING_BACK) {
                cameraId = i;
                cameraFront = false;
                break;

            }

        }
        return cameraId;
    }

    public void onResume() {

            super.onResume();
            if(mCamera == null) {
                mCamera = Camera.open();
                mCamera.setDisplayOrientation(90);
                mPicture = getPictureCallback();
                mPreview.refreshCamera(mCamera);
                Log.d("nu", "null");
            }else {
                Log.d("nu","no null");
            }

    }


    @Override
    protected void onPause() {
        super.onPause();
        //when on Pause, release camera in order to be used from other applications
        releaseCamera();
    }

    private void releaseCamera() {
        // stop and release camera
        if (mCamera != null) {
            mCamera.stopPreview();
            mCamera.setPreviewCallback(null);
            mCamera.release();
            mCamera = null;
        }
    }

    public String saveImage(Bitmap myBitmap) {
        ByteArrayOutputStream bytes = new ByteArrayOutputStream();
        myBitmap.compress(Bitmap.CompressFormat.JPEG, 90, bytes);
        File wallpaperDirectory = new File(
                Environment.getExternalStorageDirectory() + IMAGE_DIRECTORY);
        // have the object build the directory structure, if needed.

        if (!wallpaperDirectory.exists()) {
            Log.d("dir", "" + wallpaperDirectory.mkdirs());
            wallpaperDirectory.mkdirs();
        }

        try {
            File f = new File(wallpaperDirectory, Calendar.getInstance()
                    .getTimeInMillis() + ".jpg");
            f.createNewFile();   //give read write permission
            FileOutputStream fo = new FileOutputStream(f);
            fo.write(bytes.toByteArray());
            MediaScannerConnection.scanFile(this,
                    new String[]{f.getPath()},
                    new String[]{"image/jpeg"}, null);
            fo.close();

            return f.getAbsolutePath();
        } catch (IOException e1) {
            e1.printStackTrace();
        }
        return "";

    }

    private Camera.PictureCallback getPictureCallback() {
        Camera.PictureCallback picture = new Camera.PictureCallback() {
            @Override
            public void onPictureTaken(byte[] data, Camera camera) {
                bitmap = BitmapFactory.decodeByteArray(data, 0, data.length);
                saveImage(MainActivity.bitmap);
            }
        };
        return picture;
    }

}
