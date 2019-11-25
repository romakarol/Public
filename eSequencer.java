import javax.sound.midi.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*; //actionListener/event
import java.util.*;
import java.awt.image.BufferedImage; //Image handling
import java.io.File;
import java.io.IOException;
import javax.imageio.ImageIO;


public class eSequencer {
 JPanel mainPanel;
 ArrayList<JCheckBox> checkboxList;
 Sequencer sequencer;
 Sequence sequence;
 Track track;
 JFrame theFrame;

 String[] instrumentNames = {"Chinese Cymbal","Tambourine","Mute Cuica",
  "Open Cuica","Claves", "Maraca","Hi-Timbale","Lo-Timbale","Hi-Agogo","Lo-Agogo",
  "Mute Conga","Open Conga","Low Conga","Cabasa","Short Guiro","Long Guiro"};
 int[] instruments={52,54,78,79,75,70,65,66,67,68,62,63,64,69,73,74};

 public static void main (String[] args){
	 new eSequencer().buildGUI();
}

 public void buildGUI(){
   theFrame=new JFrame("eSequencer");
	 theFrame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);


	 BorderLayout layout = new BorderLayout();
	 JPanel background = new JPanel(layout);
   JPanel southside = new JPanel();
   southside.setBackground(new Color(59, 96, 185));
   background.setBackground(new Color(59, 96, 185));
	 background.setBorder(BorderFactory.createEmptyBorder(10,10,10,10));

   //add Logo:
   JLabel contentPane = new JLabel();
   try{
    contentPane.setIcon(new ImageIcon(ImageIO.read(new File("./logo.png"))));
   }
   catch(IOException e){
    System.out.println("failed to load iamge");
   }
   background.add(BorderLayout.EAST, contentPane);
   //

	 checkboxList = new ArrayList<JCheckBox>();
	 Box buttonBox = new Box(BoxLayout.X_AXIS);

	 JButton start = new JButton("start");
	 start.addActionListener(new MyStartListener());
	 buttonBox.add(start);

	 JButton stop = new JButton("Stop");
     stop.addActionListener(new MyStopListener());
	 buttonBox.add(stop);

	 JButton upTempo = new JButton("Tempo Up");
     upTempo.addActionListener(new MyUpTempoListener());
	 buttonBox.add(upTempo);

	 JButton downTempo = new JButton("Tempo Down");
	 downTempo.addActionListener(new MyDownTempoListener());
     buttonBox.add(downTempo);

     String text= new String("");
     Box nameBox = new Box(BoxLayout.Y_AXIS);
     for(int i =0; i <16; i++) {
       text=instrumentNames[i];
       Label label = new Label(text);
       label.setForeground(new Color(255,53,100));
       label.setFont(new Font("Courier New", Font.BOLD, 17));
       nameBox.add(label);
     }

     southside.add(buttonBox); //place buttons in a new panel for centering
     background.add(BorderLayout.SOUTH, southside);
     background.add(BorderLayout.WEST, nameBox);

     theFrame.getContentPane().add(background);
     theFrame.setVisible(true);

     GridLayout grid = new GridLayout(16,16);
     grid.setVgap(1);
     grid.setHgap(2);
     mainPanel = new JPanel(grid);
     background.add(BorderLayout.CENTER, mainPanel);

     for(int i=0; i<256; i++) {
     JCheckBox c = new JCheckBox();
     try{
      c.setSelectedIcon(new ImageIcon(ImageIO.read(new File("./base.jpg"))));
      c.setBackground(new Color(59, 96, 185));
      c.setIcon(new ImageIcon(ImageIO.read(new File("./ticked.jpg"))));
      c.setBackground(new Color(59, 96, 185));
      c.setOpaque(true);
     }
     catch (IOException e)
     {
    System.out.println("Failed to load image");
     }

     c.setSelected(false);
     checkboxList.add(c);
     mainPanel.add(c);
     }//loop

     setUpMidi();

     theFrame.setBounds(50,50,300,300);
     theFrame.pack();
     theFrame.setVisible(true);
 }//close method

public void setUpMidi() {
  try {
    sequencer = MidiSystem.getSequencer();
    sequencer.open();
    sequence = new Sequence(Sequence.PPQ,4);
    track = sequence.createTrack();
    sequencer.setTempoInBPM(120);

  } catch(Exception e) {e.printStackTrace();}
}//close method

public void buildTrackAndStart(){
  int[] trackList = null;

  sequence.deleteTrack(track);
  track = sequence.createTrack();

  for(int i =0; i<16; i++) {
    trackList = new int[16];

    int key = instruments[i];

    for(int j =0; j<16; j++){
      JCheckBox jc = (JCheckBox) checkboxList.get(j + (16*i));
      if (jc.isSelected()) {
        trackList[j] = key;
      } else {
        trackList[j] = 0;
      }
    }

    makeTracks(trackList);
    track.add(makeEvent(176,1,127,0,16)); //control change
  }//close outer

  track.add(makeEvent(192,9,1,0,15)); //program change
  try{

    sequencer.setSequence(sequence);
    sequencer.setLoopCount(sequencer.LOOP_CONTINUOUSLY);
    sequencer.start();
    sequencer.setTempoInBPM(120);
  } catch(Exception e) {e.printStackTrace();}
}//close buildtrackandstart

public class MyStartListener implements ActionListener {
  public void actionPerformed(ActionEvent a){
    buildTrackAndStart();
  }
}

public class MyStopListener implements ActionListener {
    public void actionPerformed(ActionEvent a){
      sequencer.stop();
    }
  }

  public class MyUpTempoListener implements ActionListener {
      public void actionPerformed(ActionEvent a){
        float tempoFactor = sequencer.getTempoFactor();
        sequencer.setTempoFactor ((float) (tempoFactor * 1.03));
      }
    }

    public class MyDownTempoListener implements ActionListener {
        public void actionPerformed(ActionEvent a){
          float tempoFactor = sequencer.getTempoFactor();
          sequencer.setTempoFactor ((float) (tempoFactor * .97));
        }
      }//close inner class

        public void makeTracks (int[] list) {

            for(int i =0; i<16; i++) {
                int key = list[i];

                if (key != 0) {
                  track.add(makeEvent(144,9,key, 100, i)); //Note On
                  track.add(makeEvent(128,9,key, 100, i+1)); //Off 1 beat later
                }
            }
        }//makeTracks

        public MidiEvent makeEvent(int comd, int chan, int one, int two, int tick) {
          MidiEvent event = null;
          try{
            ShortMessage a = new ShortMessage();
            a.setMessage(comd, chan, one, two);
            event = new MidiEvent(a, tick);

          } catch (Exception e) {e.printStackTrace(); }
          return event;
        }//MidiEvent

      }//close class
