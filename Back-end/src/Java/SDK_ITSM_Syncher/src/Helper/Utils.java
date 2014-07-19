package Helper;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.net.*;
import java.io.*;
import java.text.Normalizer;
import java.util.regex.Pattern;


public class Utils
{
	private static final String base64code = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            + "abcdefghijklmnopqrstuvwxyz" + "0123456789" + "+/";
	private static final int splitLinesAt = 76;

    public Utils()
    {
    }

    public static byte[] zeroPad(int length, byte[] bytes) {
        byte[] padded = new byte[length]; // initialized to zero by JVM
        System.arraycopy(bytes, 0, padded, 0, bytes.length);
        return padded;
    }
    
    public static String splitLines(String string) {
   	 
        String lines = "";
        for (int i = 0; i < string.length(); i += splitLinesAt) {
 
            lines += string.substring(i, Math.min(string.length(), i + splitLinesAt));
            lines += "\r\n";
 
        }
        return lines;
 
    }
    
    public static String Base64Encode(String string) {
   	 
        String encoded = "";
        byte[] stringArray;
        try {
            stringArray = string.getBytes("UTF-8");  // use appropriate encoding string!
        } catch (Exception ignored) {
            stringArray = string.getBytes();  // use locale default rather than croak
        }
        // determine how many padding bytes to add to the output
        int paddingCount = (3 - (stringArray.length % 3)) % 3;
        // add any necessary padding to the input
        stringArray = zeroPad(stringArray.length + paddingCount, stringArray);
        // process 3 bytes at a time, churning out 4 output bytes
        // worry about CRLF insertions later
        for (int i = 0; i < stringArray.length; i += 3) {
            int j = ((stringArray[i] & 0xff) << 16) +
                ((stringArray[i + 1] & 0xff) << 8) + 
                (stringArray[i + 2] & 0xff);
            encoded = encoded + base64code.charAt((j >> 18) & 0x3f) +
                base64code.charAt((j >> 12) & 0x3f) +
                base64code.charAt((j >> 6) & 0x3f) +
                base64code.charAt(j & 0x3f);
        }
        // replace encoded padding nulls with "="
        return splitLines(encoded.substring(0, encoded.length() -
            paddingCount) + "==".substring(0, paddingCount));
 
    }
    
    public static int GetShiftID()
    {
        DateFormat dtfm = new SimpleDateFormat("HHmmss");
        int currenttime = Integer.parseInt(dtfm.format(new Date()));
        if(currenttime >= 0x35b60 || currenttime <= 0x128b7)
        {
            return 3;
        }
        return currenttime < 0x249f0 ? 1 : 2;
    }

    public static String GetShiftDate()
    {
        DateFormat dtfm1 = new SimpleDateFormat("HHmmss");
        DateFormat dtfm2 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        int currenttime = Integer.parseInt(dtfm1.format(new Date()));
        int current_shift_id = GetShiftID();
        Calendar c1 = Calendar.getInstance();
        c1.setTime(new Date());
        if(current_shift_id == 3 && currenttime <= 0x128b7)
        {
            c1.add(5, -1);
        }
        return dtfm2.format(c1.getTime());
    }
    
    public static String SendSMS(String strPhoneNumber, String strMessage, String strServiceURL){
    	String strResult = "";
    	try{
	    	strPhoneNumber = strPhoneNumber.replace(" ", "");
	    	if(strPhoneNumber.length() >= 10){
		    	strPhoneNumber = "84".concat(strPhoneNumber.substring(1));
		
		    	strServiceURL = strServiceURL.concat(strPhoneNumber).concat("&msg=").concat(URLEncoder.encode(Base64Encode(UnAccent(strMessage)), "UTF-8"));
		    	System.out.println(strServiceURL);
		    	
		    	URL oURL = new URL(strServiceURL);
		        URLConnection oHTTPConnection = oURL.openConnection();
		        BufferedReader in = new BufferedReader(new InputStreamReader(oHTTPConnection.getInputStream()));
		        String inputLine;
	
		        while ((inputLine = in.readLine()) != null){
		        	inputLine = inputLine.replace("\r\n", "").replace("\n", "").trim();
		        	if(!inputLine.isEmpty()){
		        		strResult = strResult.concat(inputLine);
		        	}
		        }
		        in.close();
	    	}
    	} catch(Exception e){
    		return "System error: ".concat(e.getMessage());
    	}
    	return strResult;
    }
    
    public static String UnAccent(String s){
    	String temp = Normalizer.normalize(s, Normalizer.Form.NFD);
        Pattern pattern = Pattern.compile("\\p{InCombiningDiacriticalMarks}+");
        return pattern.matcher(temp).replaceAll("").replaceAll("Đ", "D").replace("đ", "d");
    }
    
    
}
